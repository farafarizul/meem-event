<?php

namespace App\Services;

use App\Models\SilverPrice;
use App\Models\SilverPriceDaily;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SilverPriceDailyAggregatorService
{
    public function __construct(private GroqSilverReasonService $groqService) {}

    /**
     * Sync a single date: aggregate from silver_price, upsert, then call AI.
     *
     * @return array{status: string, message: string}
     */
    public function syncDate(string $date, bool $withAi = true): array
    {
        $targetDate = Carbon::parse($date)->toDateString();

        Log::info('SilverPriceDailyAggregatorService: Start processing.', ['date' => $targetDate]);

        $rows = SilverPrice::whereDate('last_updated', $targetDate)->get();

        if ($rows->isEmpty()) {
            Log::info('SilverPriceDailyAggregatorService: No source rows found.', ['date' => $targetDate]);
            return ['status' => 'no_data', 'message' => "No silver_price rows found for {$targetDate}."];
        }

        $buyPrices = $rows->pluck('buy_price')->map(fn ($v) => (float) $v);

        $avgSell    = $rows->avg('sell_price');
        $avgBuy     = $buyPrices->avg();
        $highestBuy = $buyPrices->max();
        $lowestBuy  = $buyPrices->min();

        $ordered    = $rows->sortBy('last_updated');
        $openPrice  = (float) $ordered->first()->buy_price;
        $closePrice = (float) $ordered->last()->buy_price;

        $daily = SilverPriceDaily::updateOrCreate(
            ['silver_price_date' => $targetDate],
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

        Log::info('SilverPriceDailyAggregatorService: Daily summary upserted.', [
            'date'       => $targetDate,
            'buy_price'  => $daily->buy_price,
            'sell_price' => $daily->sell_price,
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
            $dateStr           = $start->toDateString();
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
    public function regenerateAiReason(SilverPriceDaily $daily): array
    {
        $date = $daily->silver_price_date->toDateString();
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

    private function refreshAiReason(SilverPriceDaily $daily, string $date): void
    {
        try {
            $reason = $this->groqService->getReason($date);

            $daily->update(['reason_from_ai' => $reason]);

            if ($reason) {
                Log::info('SilverPriceDailyAggregatorService: AI reason saved.', ['date' => $date]);
            } else {
                Log::warning('SilverPriceDailyAggregatorService: AI reason is null; numeric summary already saved.', [
                    'date' => $date,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('SilverPriceDailyAggregatorService: AI call threw exception; numeric summary preserved.', [
                'date'  => $date,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
