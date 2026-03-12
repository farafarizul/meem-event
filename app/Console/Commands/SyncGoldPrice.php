<?php

namespace App\Console\Commands;

use App\Services\GoldPriceService;
use Illuminate\Console\Command;

class SyncGoldPrice extends Command
{
    protected $signature = 'gold-price:sync';

    protected $description = 'Sync the latest gold price from the external API into the local database.';

    public function handle(GoldPriceService $service): int
    {
        $this->info('Running gold price sync...');

        try {
            $result = $service->syncLatestGoldPrice();

            $this->line('[' . $result['status'] . '] ' . $result['message']);

        } catch (\Throwable $e) {
            $this->error('Unexpected error: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
