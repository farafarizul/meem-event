<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SilverPriceDailySeeder extends Seeder
{
    public function run(): void
    {
        $bullishReasons = [
            'Silver surged on strong industrial demand from the solar panel manufacturing sector.',
            'Bullish momentum in silver driven by a weaker US dollar and Fed dovish signals.',
            'Growing green energy transition demand boosted silver as a critical industrial metal.',
            'Safe-haven buying spilled over from gold into silver amid global uncertainty.',
            'Tight supply from major mining regions pushed silver prices higher.',
            'Positive Chinese economic data lifted industrial metals including silver.',
            'Technical breakout above resistance level triggered fresh buying in silver.',
            'Electronics sector demand recovery supported silver prices.',
            'Short-covering rally pushed silver sharply higher in thin trading.',
            'Silver gained as EV and battery storage investments accelerated globally.',
        ];

        $bearishReasons = [
            'Silver retreated as industrial demand outlook weakened on softer global PMI data.',
            'Rising US dollar weighed on silver and other dollar-denominated commodities.',
            'Profit-taking after recent run-up pushed silver to intraday lows.',
            'Concerns over slowing Chinese manufacturing activity pressured silver prices.',
            'Broader commodity sell-off dragged silver lower alongside copper and platinum.',
            'Silver declined as the Fed maintained its hawkish tone on interest rates.',
            'Lower-than-expected industrial output data dampened silver demand prospects.',
            'Technical breakdown below key support triggered stop-loss selling in silver.',
            'Strong US dollar and improving risk sentiment reduced silver\'s safe-haven appeal.',
            'Silver slipped amid fears of a global economic slowdown curbing industrial use.',
        ];

        $records   = [];
        $current   = Carbon::parse('2026-01-01');
        $yesterday = Carbon::yesterday();

        // Starting silver price in MYR per gram – realistic for early 2026
        $basePrice = 5.20;
        $reasonIdx = 0;

        while ($current->lte($yesterday)) {
            // Simulate daily price change: silver is more volatile than gold
            $changePct = (mt_rand(-200, 250) / 10000); // roughly -2.0% to +2.5%
            $basePrice = round($basePrice * (1 + $changePct), 2);

            // Generate open near previous close with small gap
            $openPrice = round($basePrice + (mt_rand(-5, 5) / 100), 2);
            $closePrice = $basePrice;

            $rangePct     = mt_rand(20, 80) / 10000;
            $highestPrice = round(max($openPrice, $closePrice) * (1 + $rangePct), 2);
            $lowestPrice  = round(min($openPrice, $closePrice) * (1 - $rangePct), 2);

            // Sell/buy spread (~1% each side for silver)
            $sellPrice = round($closePrice * 1.01, 2);
            $buyPrice  = round($closePrice * 0.99, 2);

            $candleDirection = $closePrice >= $openPrice ? 'bullish' : 'bearish';

            if ($candleDirection === 'bullish') {
                $reason = $bullishReasons[$reasonIdx % count($bullishReasons)];
            } else {
                $reason = $bearishReasons[$reasonIdx % count($bearishReasons)];
            }
            $reasonIdx++;

            $records[] = [
                'silver_price_date' => $current->toDateString(),
                'sell_price'        => $sellPrice,
                'buy_price'         => $buyPrice,
                'open_price'        => $openPrice,
                'close_price'       => $closePrice,
                'highest_price'     => $highestPrice,
                'lowest_price'      => $lowestPrice,
                'candle_direction'  => $candleDirection,
                'reason_from_ai'    => $reason,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            $current->addDay();
        }

        foreach (array_chunk($records, 200) as $chunk) {
            DB::table('silver_price_daily')->insertOrIgnore($chunk);
        }
    }
}
