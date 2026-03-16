<?php

namespace App\Console\Commands;

use App\Services\CountrySyncService;
use App\Services\IdentificationTypeSyncService;
use App\Services\IndustrySyncService;
use App\Services\StateSyncService;
use Illuminate\Console\Command;

class SyncConfigLists extends Command
{
    protected $signature = 'sync:config-lists';

    protected $description = 'Sync all config lists (states, countries, industries, identification types) from the external API.';

    public function handle(
        StateSyncService $stateService,
        CountrySyncService $countryService,
        IndustrySyncService $industryService,
        IdentificationTypeSyncService $identificationTypeService
    ): int {
        $this->info('Running config lists sync...');

        try {
            foreach ([
                'States'               => $stateService,
                'Countries'            => $countryService,
                'Industries'           => $industryService,
                'Identification Types' => $identificationTypeService,
            ] as $label => $service) {
                $this->line("Syncing {$label}...");
                $result = $service->sync();
                $this->line("  Status:   {$result['status']}");
                $this->line("  Total:    {$result['total']}");
                $this->line("  Inserted: {$result['inserted']}");
                $this->line("  Updated:  {$result['updated']}");
                $this->line("  Deleted:  {$result['deleted']}");
            }
        } catch (\Throwable $e) {
            $this->error('Unexpected error: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Config lists sync complete.');

        return self::SUCCESS;
    }
}
