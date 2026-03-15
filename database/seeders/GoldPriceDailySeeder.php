<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoldPriceDailySeeder extends Seeder
{
    public function run(): void
    {
        $bullishReasons = [
            'Gold rallied amid rising geopolitical tensions and safe-haven demand.',
            'Bullish momentum driven by a weaker US dollar and softer CPI data.',
            'Fed rate cut expectations boosted gold as real yields declined.',
            'Strong central bank buying from emerging markets supported gold prices.',
            'Global equity sell-off increased safe-haven flows into gold.',
            'Inflationary concerns pushed investors toward gold as a hedge.',
            'Technical breakout above key resistance level attracted buyers.',
            'Chinese New Year seasonal demand provided additional support for gold prices.',
            'Weaker-than-expected US jobs data lifted gold amid renewed rate cut hopes.',
            'Middle East tensions and risk-off sentiment boosted gold demand.',
        ];

        $bearishReasons = [
            'Gold declined as the US dollar strengthened following positive economic data.',
            'Bearish pressure from rising Treasury yields reduced gold\'s appeal.',
            'Profit-taking after recent gains pushed gold lower.',
            'Strong US jobs report reduced Fed rate cut expectations, weighing on gold.',
            'Risk-on sentiment in equity markets reduced safe-haven demand for gold.',
            'Gold fell as the Fed signalled a higher-for-longer interest rate stance.',
            'Stronger-than-expected US retail sales data boosted the dollar and pressured gold.',
            'Technical resistance at key levels triggered sell orders in gold.',
            'IMF upgraded global growth outlook, reducing risk-off demand for gold.',
            'Gold slipped as easing geopolitical tensions reduced safe-haven buying.',
        ];

        $records   = [];
        $current   = Carbon::parse('2026-01-01');
        $yesterday = Carbon::yesterday();

        // Starting gold price in MYR per gram (999 gold) – realistic for early 2026
        $basePrice = 420.00;
        $reasonIdx = 0;

        while ($current->lte($yesterday)) {
            // Simulate daily price change: slight upward bias with random noise
            $changePct = (mt_rand(-130, 180) / 10000); // roughly -1.3% to +1.8%
            $basePrice = round($basePrice * (1 + $changePct), 2);

            // Generate open near previous close with small gap
            $openPrice = round($basePrice + (mt_rand(-30, 30) / 100), 2);
            $closePrice = $basePrice;

            $rangePct      = mt_rand(15, 60) / 10000;
            $highestPrice  = round(max($openPrice, $closePrice) * (1 + $rangePct), 2);
            $lowestPrice   = round(min($openPrice, $closePrice) * (1 - $rangePct), 2);

            // Sell/buy spread (~0.5% each side)
            $sellPrice = round($closePrice * 1.005, 2);
            $buyPrice  = round($closePrice * 0.995, 2);

            $candleDirection = $closePrice >= $openPrice ? 'bullish' : 'bearish';

            if ($candleDirection === 'bullish') {
                $reason = $bullishReasons[$reasonIdx % count($bullishReasons)];
            } else {
                $reason = $bearishReasons[$reasonIdx % count($bearishReasons)];
            }
            $reasonIdx++;

            $records[] = [
                'gold_price_date'  => $current->toDateString(),
                'sell_price'       => $sellPrice,
                'buy_price'        => $buyPrice,
                'open_price'       => $openPrice,
                'close_price'      => $closePrice,
                'highest_price'    => $highestPrice,
                'lowest_price'     => $lowestPrice,
                'candle_direction' => $candleDirection,
                'reason_from_ai'   => $reason,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];

            $current->addDay();
        }

        foreach (array_chunk($records, 200) as $chunk) {
            DB::table('gold_price_daily')->insertOrIgnore($chunk);
        }
    }
}
