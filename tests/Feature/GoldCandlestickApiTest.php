<?php

namespace Tests\Feature;

use App\Models\GoldPriceDaily;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoldCandlestickApiTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeRecord(string $date, array $overrides = []): GoldPriceDaily
    {
        return GoldPriceDaily::create(array_merge([
            'gold_price_date' => $date,
            'open_price'      => 450.10,
            'close_price'     => 452.70,
            'highest_price'   => 454.00,
            'lowest_price'    => 449.80,
        ], $overrides));
    }

    // ── /api/gold/candlestick ─────────────────────────────────────────────────

    public function test_candlestick_returns_data_within_30_day_window(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $this->makeRecord($yesterday, [
            'open_price'    => 450.10,
            'close_price'   => 452.70,
            'highest_price' => 454.00,
            'lowest_price'  => 449.80,
        ]);

        $response = $this->getJson('/api/gold/candlestick');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Gold candlestick data retrieved successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['timestamp', 'date', 'open', 'close', 'high', 'low'],
                ],
            ]);
    }

    public function test_candlestick_timestamp_is_in_milliseconds(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $this->makeRecord($yesterday);

        $response = $this->getJson('/api/gold/candlestick');

        $response->assertStatus(200);

        $timestamp = $response->json('data.0.timestamp');
        $this->assertIsInt($timestamp);
        // Millisecond timestamps are > 10^12; second timestamps are ~10^9
        $this->assertGreaterThan(1_000_000_000_000, $timestamp);
    }

    public function test_candlestick_excludes_todays_data(): void
    {
        $today     = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        $this->makeRecord($today);
        $this->makeRecord($yesterday);

        $response = $this->getJson('/api/gold/candlestick');

        $response->assertStatus(200);

        $dates = collect($response->json('data'))->pluck('date');
        $this->assertNotContains($today, $dates->all());
        $this->assertContains($yesterday, $dates->all());
    }

    public function test_candlestick_excludes_data_older_than_30_days(): void
    {
        $yesterday    = Carbon::yesterday();
        $thirtyOneDaysAgo = $yesterday->copy()->subDays(30)->toDateString();
        $thirtyDaysAgo    = $yesterday->copy()->subDays(29)->toDateString();

        $this->makeRecord($thirtyOneDaysAgo);
        $this->makeRecord($thirtyDaysAgo);

        $response = $this->getJson('/api/gold/candlestick');

        $response->assertStatus(200);

        $dates = collect($response->json('data'))->pluck('date');
        $this->assertNotContains($thirtyOneDaysAgo, $dates->all());
        $this->assertContains($thirtyDaysAgo, $dates->all());
    }

    public function test_candlestick_returns_results_sorted_ascending(): void
    {
        $yesterday = Carbon::yesterday();

        $this->makeRecord($yesterday->toDateString());
        $this->makeRecord($yesterday->copy()->subDays(1)->toDateString());
        $this->makeRecord($yesterday->copy()->subDays(2)->toDateString());

        $response = $this->getJson('/api/gold/candlestick');

        $response->assertStatus(200);

        $dates = collect($response->json('data'))->pluck('date')->all();
        $sorted = $dates;
        sort($sorted);
        $this->assertEquals($sorted, $dates);
    }

    public function test_candlestick_returns_numeric_ohlc_values(): void
    {
        $this->makeRecord(Carbon::yesterday()->toDateString(), [
            'open_price'    => 450.10,
            'close_price'   => 452.70,
            'highest_price' => 454.00,
            'lowest_price'  => 449.80,
        ]);

        $response = $this->getJson('/api/gold/candlestick');

        $response->assertStatus(200);

        $item = $response->json('data.0');
        $this->assertIsFloat($item['open']);
        $this->assertIsFloat($item['close']);
        $this->assertIsFloat($item['high']);
        $this->assertIsFloat($item['low']);
    }

    public function test_candlestick_returns_empty_data_when_no_records(): void
    {
        $response = $this->getJson('/api/gold/candlestick');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'No gold candlestick data found',
                'data'    => [],
            ]);
    }
}
