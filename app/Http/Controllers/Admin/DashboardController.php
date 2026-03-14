<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApkDetail;
use App\Models\Event;
use App\Models\EventCheckin;
use App\Models\GoldPrice;
use App\Models\GoldPriceDaily;
use App\Models\SilverPrice;
use App\Models\SilverPriceDaily;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // --- Summary card counts ---
        $latestGold   = GoldPrice::latest('last_updated')->first();
        $latestSilver = SilverPrice::latest('last_updated')->first();

        $stats = [
            'total_users'         => User::where('is_admin', false)->count(),
            'total_events'        => Event::count(),
            'total_checkins'      => EventCheckin::count(),
            'total_online_events' => Event::where('category_event', 'online')->count(),
            'total_onsite_events' => Event::where('category_event', 'onsite')->count(),
            'gold_sell_price'     => $latestGold?->sell_price,
            'gold_buy_price'      => $latestGold?->buy_price,
            'silver_sell_price'   => $latestSilver?->sell_price,
            'silver_buy_price'    => $latestSilver?->buy_price,
        ];

        // --- Monthly chart data (last 12 months) ---
        $twelveMonthsAgo = Carbon::now()->subMonths(11)->startOfMonth();

        [$goldChartLabels, $goldChartSell, $goldChartBuy] = $this->getMonthlyChartData(
            GoldPriceDaily::class, 'gold_price_date', $twelveMonthsAgo
        );

        [$silverChartLabels, $silverChartSell, $silverChartBuy] = $this->getMonthlyChartData(
            SilverPriceDaily::class, 'silver_price_date', $twelveMonthsAgo
        );

        // --- Recent / Upcoming lists ---
        $recent_checkins = EventCheckin::with(['user', 'event'])
            ->latest('checked_in_at')
            ->take(10)
            ->get();

        $upcoming_events = Event::with('branch')
            ->where('start_date', '>=', Carbon::today())
            ->orderBy('start_date')
            ->take(10)
            ->get();

        $latest_users = User::where('is_admin', false)
            ->latest()
            ->take(10)
            ->get();

        // --- APK management ---
        $apk_list = ApkDetail::latest('uploaded_date')->get();

        return view('admin.dashboard', compact(
            'stats',
            'goldChartLabels', 'goldChartSell', 'goldChartBuy',
            'silverChartLabels', 'silverChartSell', 'silverChartBuy',
            'recent_checkins',
            'upcoming_events',
            'latest_users',
            'apk_list'
        ));
    }

    /**
     * Aggregate monthly avg sell/buy prices for a given daily-price model.
     *
     * @param  class-string  $modelClass  Eloquent model (GoldPriceDaily|SilverPriceDaily)
     * @param  string        $dateColumn  Name of the date column in that table
     * @param  Carbon        $since       Earliest date to include
     * @return array{Collection, Collection, Collection}  [labels, sell, buy]
     */
    private function getMonthlyChartData(string $modelClass, string $dateColumn, Carbon $since): array
    {
        $rows = $modelClass::select(
                DB::raw("DATE_FORMAT({$dateColumn}, '%Y-%m') as month"),
                DB::raw('AVG(sell_price) as avg_sell'),
                DB::raw('AVG(buy_price) as avg_buy')
            )
            ->where($dateColumn, '>=', $since)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = $rows->map(function ($row) {
            try {
                return Carbon::createFromFormat('Y-m', $row->month)->format('M Y');
            } catch (\Exception $e) {
                return $row->month;
            }
        })->values();

        $sell = $rows->pluck('avg_sell')->map(fn ($v) => round((float) $v, 2))->values();
        $buy  = $rows->pluck('avg_buy')->map(fn ($v) => round((float) $v, 2))->values();

        return [$labels, $sell, $buy];
    }
}
