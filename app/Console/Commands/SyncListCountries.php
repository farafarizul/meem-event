<?php

namespace App\Console\Commands;

use App\Services\CountrySyncService;
use Illuminate\Console\Command;

class SyncListCountries extends Command
{
    protected $signature = 'sync:list-countries';

    protected $description = 'Sync the list of countries from the external API.';

    public function handle(CountrySyncService $service): int
    {
        $this->info('Running countries sync...');

        try {
            $result = $service->sync();

            $this->line("Status:   {$result['status']}");
            $this->line("Total:    {$result['total']}");
            $this->line("Inserted: {$result['inserted']}");
            $this->line("Updated:  {$result['updated']}");
            $this->line("Deleted:  {$result['deleted']}");

        } catch (\Throwable $e) {
            $this->error('Unexpected error: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
