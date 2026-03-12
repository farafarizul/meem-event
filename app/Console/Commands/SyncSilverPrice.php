<?php

namespace App\Console\Commands;

use App\Services\SilverPriceService;
use Illuminate\Console\Command;

class SyncSilverPrice extends Command
{
    protected $signature = 'silver-price:sync';

    protected $description = 'Sync the latest silver price from the external API into the local database.';

    public function handle(SilverPriceService $service): int
    {
        $this->info('Running silver price sync...');

        try {
            $result = $service->syncLatestSilverPrice();

            $this->line('[' . $result['status'] . '] ' . $result['message']);

        } catch (\Throwable $e) {
            $this->error('Unexpected error: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
