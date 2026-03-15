<?php

namespace App\Console\Commands;

use App\Services\IndustrySyncService;
use Illuminate\Console\Command;

class SyncListIndustries extends Command
{
    protected $signature = 'sync:list-industries';

    protected $description = 'Sync the list of industries from the external API.';

    public function handle(IndustrySyncService $service): int
    {
        $this->info('Running industries sync...');

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
