<?php

namespace App\Services;

use App\Models\GoldPrice;
use App\Models\GoldPriceDaily;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoldPriceDailyAggregatorService
{
    public function __construct(private GroqGoldReasonService $groqService) {}

    /**
     * Sync a single date: aggregate from gold_price, upsert, then call AI.
     *
     * @return array{status: string, message: string}
     */
    public function syncDate(string $date, bool $withAi = true): array
    {
        $targetDate = Carbon::parse($date)->toDateString();

        Log::info('GoldPriceDailyAggregatorService: Start processing.', ['date' => $targetDate]);

        $rows = GoldPrice::whereDate('last_updated', $targetDate)->get();

        if ($rows->isEmpty()) {
            Log::info('GoldPriceDailyAggregatorService: No source rows found.', ['date' => $targetDate]);
            return ['status' => 'no_data', 'message' => "No gold_price rows found for {$targetDate}."];
        }

        $buyPrices = $rows->pluck('buy_price')->map(fn ($v) => (float) $v);

        $avgSell     = $rows->avg('sell_price');
        $avgBuy      = $buyPrices->avg();
        $highestBuy  = $buyPrices->max();
        $lowestBuy   = $buyPrices->min();

        $ordered   = $rows->sortBy('last_updated');
        $openPrice = (float) $ordered->first()->buy_price;
        $closePrice = (float) $ordered->last()->buy_price;

        $daily = GoldPriceDaily::updateOrCreate(
            ['gold_price_date' => $targetDate],
            [
                'sell_price'       => round($avgSell, 2),
                'buy_price'        => round($avgBuy, 2),
                'open_price'       => round($openPrice, 2),
                'close_price'      => round($closePrice, 2),
                'highest_price'    => round($highestBuy, 2),
                'lowest_price'     => round($lowestBuy, 2),
                'candle_direction' => $this->resolveCandleDirection($openPrice, $closePrice),
            ]
        );

        Log::info('GoldPriceDailyAggregatorService: Daily summary upserted.', [
            'date'        => $targetDate,
            'buy_price'   => $daily->buy_price,
            'sell_price'  => $daily->sell_price,
        ]);

        if ($withAi) {
            $this->refreshAiReason($daily, $targetDate);
        }

        return ['status' => 'success', 'message' => "Daily summary synced successfully for {$targetDate}."];
    }

    /**
     * Sync a range of dates.
     *
     * @return array<string, array{status: string, message: string}>
     */
    public function syncDateRange(string $from, string $to, bool $withAi = true): array
    {
        $start   = Carbon::parse($from);
        $end     = Carbon::parse($to);
        $results = [];

        while ($start->lte($end)) {
            $dateStr          = $start->toDateString();
            $results[$dateStr] = $this->syncDate($dateStr, $withAi);
            $start->addDay();
        }

        return $results;
    }

    /**
     * Re-run only the AI reason for an existing daily record.
     *
     * @return array{status: string, message: string}
     */
    public function regenerateAiReason(GoldPriceDaily $daily): array
    {
        $date = $daily->gold_price_date->toDateString();
        $this->refreshAiReason($daily, $date);

        $daily->refresh();

        if ($daily->reason_from_ai) {
            return ['status' => 'success', 'message' => "AI reason regenerated for {$date}."];
        }

        return ['status' => 'ai_failed', 'message' => "AI reason could not be generated for {$date}."];
    }

    private function resolveCandleDirection(?float $openPrice, ?float $closePrice): string
    {
        if ($openPrice === null || $closePrice === null) {
            return 'neutral';
        }

        if ($openPrice < $closePrice) {
            return 'positive';
        }

        if ($openPrice > $closePrice) {
            return 'negative';
        }

        return 'neutral';
    }

    private function refreshAiReason(GoldPriceDaily $daily, string $date): void
    {
        try {
            $reason = $this->groqService->getReason($date);

            $daily->update(['reason_from_ai' => $reason]);

            if ($reason) {
                Log::info('GoldPriceDailyAggregatorService: AI reason saved.', ['date' => $date]);
            } else {
                Log::warning('GoldPriceDailyAggregatorService: AI reason is null; numeric summary already saved.', [
                    'date' => $date,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('GoldPriceDailyAggregatorService: AI call threw exception; numeric summary preserved.', [
                'date'  => $date,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
