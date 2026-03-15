<?php

namespace App\Console\Commands;

use App\Services\StateSyncService;
use Illuminate\Console\Command;

class SyncListStates extends Command
{
    protected $signature = 'sync:list-states';

    protected $description = 'Sync the list of states from the external API.';

    public function handle(StateSyncService $service): int
    {
        $this->info('Running states sync...');

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
