<?php

namespace App\Services;

use App\Models\ListState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StateSyncService
{
    private const API_URL = 'https://meem.com.my/api/v1/configs/state/list';

    public function sync(): array
    {
        $data = $this->fetchFromApi();

        if ($data === null) {
            return ['status' => 'failed', 'total' => 0, 'inserted' => 0, 'updated' => 0, 'deleted' => 0];
        }

        $inserted = 0;
        $updated  = 0;
        $deleted  = 0;

        DB::transaction(function () use ($data, &$inserted, &$updated, &$deleted) {
            $apiIds = collect($data)->pluck('id')->all();

            foreach ($data as $item) {
                $result = ListState::updateOrCreate(
                    ['id' => $item['id']],
                    ['name' => $item['name']]
                );

                $result->wasRecentlyCreated ? $inserted++ : $updated++;
            }

            $deleted = ListState::whereNotIn('id', $apiIds)->delete();
        });

        Log::info('StateSyncService: Sync complete.', compact('inserted', 'updated', 'deleted'));

        return [
            'status'   => 'success',
            'total'    => count($data),
            'inserted' => $inserted,
            'updated'  => $updated,
            'deleted'  => $deleted,
        ];
    }

    private function fetchFromApi(): ?array
    {
        try {
            $response = Http::timeout(15)->get(self::API_URL);

            if (!$response->successful()) {
                Log::error('StateSyncService: API request failed.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $json = $response->json();

            if (empty($json['success']) || !isset($json['data'])) {
                Log::error('StateSyncService: Invalid API response.', ['response' => $json]);
                return null;
            }

            return $json['data'];

        } catch (\Throwable $e) {
            Log::error('StateSyncService: Exception.', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
