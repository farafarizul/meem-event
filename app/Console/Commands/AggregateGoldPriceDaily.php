<?php

namespace App\Console\Commands;

use App\Services\GoldPriceDailyAggregatorService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AggregateGoldPriceDaily extends Command
{
    protected $signature = 'gold-price:aggregate-daily
                            {--date= : Process a single date (YYYY-MM-DD). Defaults to yesterday.}
                            {--from= : Start of date range (YYYY-MM-DD).}
                            {--to=   : End of date range (YYYY-MM-DD).}';

    protected $description = 'Aggregate daily gold price summary from gold_price table and generate AI reason via Groq.';

    public function handle(GoldPriceDailyAggregatorService $service): int
    {
        $dateOpt = $this->option('date');
        $fromOpt = $this->option('from');
        $toOpt   = $this->option('to');

        if ($fromOpt && $toOpt) {
            return $this->processRange($service, $fromOpt, $toOpt);
        }

        $date = $dateOpt ? Carbon::parse($dateOpt)->toDateString() : Carbon::yesterday()->toDateString();
        return $this->processSingleDate($service, $date);
    }

    private function processSingleDate(GoldPriceDailyAggregatorService $service, string $date): int
    {
        $this->info("Processing date: {$date}");

        $result = $service->syncDate($date);

        $this->outputResult($date, $result);

        return $result['status'] === 'success' ? self::SUCCESS : self::FAILURE;
    }

    private function processRange(GoldPriceDailyAggregatorService $service, string $from, string $to): int
    {
        $this->info("Processing date range: {$from} → {$to}");

        $results   = $service->syncDateRange($from, $to);
        $success   = 0;
        $failed    = 0;
        $noData    = 0;

        foreach ($results as $date => $result) {
            $this->outputResult($date, $result);

            match ($result['status']) {
                'success'  => $success++,
                'no_data'  => $noData++,
                default    => $failed++,
            };
        }

        $this->info("Completed. Success: {$success} | No data: {$noData} | Failed: {$failed}");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function outputResult(string $date, array $result): void
    {
        match ($result['status']) {
            'success'  => $this->info("[{$date}] ✓ " . $result['message']),
            'no_data'  => $this->warn("[{$date}] No data — " . $result['message']),
            'ai_failed' => $this->warn("[{$date}] AI failed — " . $result['message']),
            default    => $this->error("[{$date}] ✗ " . $result['message']),
        };
    }
}
