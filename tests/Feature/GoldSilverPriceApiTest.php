<?php

namespace Tests\Feature;

use App\Models\GoldPrice;
use App\Models\SilverPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoldSilverPriceApiTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeGold(array $overrides = []): GoldPrice
    {
        return GoldPrice::create(array_merge([
            'type'         => 'GSS',
            'product'      => 'Gold (Au)',
            'unit'         => 'gram',
            'currency'     => 'MYR',
            'sell_price'   => 691.00,
            'buy_price'    => 640.00,
            'timezone'     => 'Asia/Kuala_Lumpur',
            'last_updated' => '2026-03-13 05:15:02',
        ], $overrides));
    }

    private function makeSilver(array $overrides = []): SilverPrice
    {
        return SilverPrice::create(array_merge([
            'type'         => 'SSS',
            'product'      => 'Silver (Ag)',
            'unit'         => 'gram',
            'currency'     => 'MYR',
            'sell_price'   => 14.50,
            'buy_price'    => 11.70,
            'timezone'     => 'Asia/Kuala_Lumpur',
            'last_updated' => '2026-03-13 05:15:04',
        ], $overrides));
    }

    // ── /api/gold_price ───────────────────────────────────────────────────────

    public function test_gold_price_returns_latest_record(): void
    {
        $this->makeGold(['last_updated' => '2026-03-13 04:00:00', 'sell_price' => 680.00, 'buy_price' => 630.00]);
        $this->makeGold(['last_updated' => '2026-03-13 05:15:02', 'sell_price' => 691.00, 'buy_price' => 640.00]);

        $response = $this->getJson('/api/gold_price');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'type'         => 'GSS',
                    'product'      => 'Gold (Au)',
                    'unit'         => 'gram',
                    'currency'     => 'MYR',
                    'sell_price'   => '691.00',
                    'buy_price'    => '640.00',
                    'timezone'     => 'Asia/Kuala_Lumpur',
                    'last_updated' => '13-03-2026 05:15:02',
                ],
            ]);
    }

    public function test_gold_price_returns_404_when_no_data(): void
    {
        $response = $this->getJson('/api/gold_price');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    // ── /api/silver_price ─────────────────────────────────────────────────────

    public function test_silver_price_returns_latest_record(): void
    {
        $this->makeSilver(['last_updated' => '2026-03-13 04:00:00', 'sell_price' => 13.00, 'buy_price' => 10.50]);
        $this->makeSilver(['last_updated' => '2026-03-13 05:15:04', 'sell_price' => 14.50, 'buy_price' => 11.70]);

        $response = $this->getJson('/api/silver_price');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'type'         => 'SSS',
                    'product'      => 'Silver (Ag)',
                    'unit'         => 'gram',
                    'currency'     => 'MYR',
                    'sell_price'   => '14.50',
                    'buy_price'    => '11.70',
                    'timezone'     => 'Asia/Kuala_Lumpur',
                    'last_updated' => '13-03-2026 05:15:04',
                ],
            ]);
    }

    public function test_silver_price_returns_404_when_no_data(): void
    {
        $response = $this->getJson('/api/silver_price');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    // ── /api/gold_and_silver_price ────────────────────────────────────────────

    public function test_gold_and_silver_price_returns_both(): void
    {
        $this->makeGold();
        $this->makeSilver();

        $response = $this->getJson('/api/gold_and_silver_price');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'gold' => [
                        'type'         => 'GSS',
                        'product'      => 'Gold (Au)',
                        'sell_price'   => '691.00',
                        'buy_price'    => '640.00',
                        'last_updated' => '13-03-2026 05:15:02',
                    ],
                    'silver' => [
                        'type'         => 'SSS',
                        'product'      => 'Silver (Ag)',
                        'sell_price'   => '14.50',
                        'buy_price'    => '11.70',
                        'last_updated' => '13-03-2026 05:15:04',
                    ],
                ],
            ]);
    }

    public function test_gold_and_silver_price_returns_404_when_no_data(): void
    {
        $response = $this->getJson('/api/gold_and_silver_price');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_gold_and_silver_price_returns_null_silver_when_only_gold_exists(): void
    {
        $this->makeGold();

        $response = $this->getJson('/api/gold_and_silver_price');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'gold'   => ['type' => 'GSS'],
                    'silver' => null,
                ],
            ]);
    }

    public function test_gold_and_silver_price_returns_null_gold_when_only_silver_exists(): void
    {
        $this->makeSilver();

        $response = $this->getJson('/api/gold_and_silver_price');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'gold'   => null,
                    'silver' => ['type' => 'SSS'],
                ],
            ]);
    }
}
