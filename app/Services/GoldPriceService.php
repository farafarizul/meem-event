<?php

namespace App\Services;

use App\Models\GoldPrice;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoldPriceService
{
    private const API_URL   = 'https://meem.com.my/api/v1/price/gwa';
    private const SETTING_KEY = 'gold_price_sync_interval_minutes';

    public function getSyncIntervalMinutes(): int
    {
        return (int) SystemSetting::getValue(self::SETTING_KEY, 5);
    }

    public function setSyncIntervalMinutes(int $minutes): void
    {
        SystemSetting::setValue(self::SETTING_KEY, (string) $minutes);

        Log::info('GoldPriceService: Admin changed sync interval.', ['interval_minutes' => $minutes]);
    }

    public function getLatestRecord(): ?GoldPrice
    {
        return GoldPrice::orderByDesc('gold_price_id')->first();
    }

    public function shouldSyncNow(): bool
    {
        $intervalMinutes = $this->getSyncIntervalMinutes();
        $latest = $this->getLatestRecord();

        if ($latest === null) {
            return true;
        }

        $nextSyncAt = $latest->created_at->addMinutes($intervalMinutes);

        return now()->gte($nextSyncAt);
    }

    public function fetchFromApi(): ?array
    {
        try {
            $response = Http::withToken(config('services.meem.gold_price_token'))
                ->timeout(15)
                ->get(self::API_URL);

            if (!$response->successful()) {
                Log::error('GoldPriceService: API request failed.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $json = $response->json();

            if (empty($json['success']) || empty($json['data'])) {
                Log::error('GoldPriceService: Invalid API response structure.', ['response' => $json]);
                return null;
            }

            return $json['data'];

        } catch (\Throwable $e) {
            Log::error('GoldPriceService: API request exception.', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function syncLatestGoldPrice(): array
    {
        if (!$this->shouldSyncNow()) {
            $interval = $this->getSyncIntervalMinutes();
            Log::info('GoldPriceService: Sync skipped — configured interval not reached.', [
                'interval_minutes' => $interval,
            ]);
            return ['status' => 'skipped_interval', 'message' => "Sync skipped: interval of {$interval} min not reached."];
        }

        return $this->doSync();
    }

    public function forceSyncLatestGoldPrice(): array
    {
        return $this->doSync();
    }

    private function doSync(): array
    {
        $data = $this->fetchFromApi();

        if ($data === null) {
            return ['status' => 'failed', 'message' => 'Sync failed: API call unsuccessful.'];
        }

        try {
            $apiLastUpdated = Carbon::createFromFormat('d-m-Y H:i:s', $data['last_updated']);
        } catch (\Throwable $e) {
            Log::error('GoldPriceService: Failed to parse last_updated from API.', [
                'last_updated' => $data['last_updated'] ?? null,
                'error'        => $e->getMessage(),
            ]);
            return ['status' => 'failed', 'message' => 'Sync failed: invalid last_updated format.'];
        }

        $latest = $this->getLatestRecord();

        if ($latest && $latest->last_updated->eq($apiLastUpdated)) {
            Log::info('GoldPriceService: Duplicate last_updated — insert skipped.', [
                'last_updated' => $data['last_updated'],
            ]);
            return ['status' => 'skipped_duplicate', 'message' => 'Sync skipped: last_updated unchanged.'];
        }

        GoldPrice::create([
            'type'         => $data['type'] ?? null,
            'product'      => $data['product'] ?? null,
            'unit'         => $data['unit'] ?? null,
            'currency'     => $data['currency'] ?? null,
            'sell_price'   => $data['sell_price'],
            'buy_price'    => $data['buy_price'],
            'timezone'     => $data['timezone'] ?? null,
            'last_updated' => $apiLastUpdated,
        ]);

        Log::info('GoldPriceService: New gold price record inserted.', [
            'last_updated' => $data['last_updated'],
            'sell_price'   => $data['sell_price'],
            'buy_price'    => $data['buy_price'],
        ]);

        return ['status' => 'inserted', 'message' => 'Sync successful: new record inserted.'];
    }
}
