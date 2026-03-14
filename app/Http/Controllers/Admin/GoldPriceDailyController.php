<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldPrice;
use App\Models\GoldPriceDaily;
use App\Services\GoldPriceDailyAggregatorService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GoldPriceDailyController extends Controller
{
    public function __construct(private GoldPriceDailyAggregatorService $service) {}

    public function index()
    {
        $stats = $this->buildStats();
        return view('admin.gold-price-daily.index', compact('stats'));
    }

    public function datatable(Request $request)
    {
        $query = GoldPriceDaily::select([
            'gold_price_daily_id',
            'gold_price_date',
            'sell_price',
            'buy_price',
            'open_price',
            'close_price',
            'highest_price',
            'lowest_price',
            'reason_from_ai',
            'created_at',
            'updated_at',
        ]);

        if ($request->filled('date_from')) {
            $query->where('gold_price_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('gold_price_date', '<=', $request->date_to);
        }

        if ($request->filled('has_reason')) {
            if ($request->has_reason === '1') {
                $query->whereNotNull('reason_from_ai');
            } elseif ($request->has_reason === '0') {
                $query->whereNull('reason_from_ai');
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('gold_price_date', fn ($row) => $row->gold_price_date->format('d M Y'))
            ->editColumn('sell_price',      fn ($row) => $row->sell_price !== null ? 'MYR ' . number_format($row->sell_price, 2) : '-')
            ->editColumn('buy_price',       fn ($row) => $row->buy_price !== null  ? 'MYR ' . number_format($row->buy_price, 2)  : '-')
            ->editColumn('open_price',      fn ($row) => $row->open_price !== null  ? 'MYR ' . number_format($row->open_price, 2)  : '-')
            ->editColumn('close_price',     fn ($row) => $row->close_price !== null ? 'MYR ' . number_format($row->close_price, 2) : '-')
            ->editColumn('highest_price',   fn ($row) => $row->highest_price !== null ? 'MYR ' . number_format($row->highest_price, 2) : '-')
            ->editColumn('lowest_price',    fn ($row) => $row->lowest_price !== null  ? 'MYR ' . number_format($row->lowest_price, 2)  : '-')
            ->editColumn('reason_from_ai',  fn ($row) => $row->reason_from_ai
                ? '<span title="' . e($row->reason_from_ai) . '">' . e(mb_substr($row->reason_from_ai, 0, 80)) . (mb_strlen($row->reason_from_ai) > 80 ? '…' : '') . '</span>'
                : '<span class="text-muted">—</span>')
            ->editColumn('created_at', fn ($row) => $row->created_at->format('d M Y H:i'))
            ->editColumn('updated_at', fn ($row) => $row->updated_at->format('d M Y H:i'))
            ->addColumn('action', function ($row) {
                $view = '<a href="' . route('admin.gold-price-daily.show', $row->gold_price_daily_id) . '" class="btn btn-sm btn-info me-1"><i class="bi bi-eye-fill"></i> View</a>';

                $sync = '<form method="POST" action="' . route('admin.gold-price-daily.sync-this', $row->gold_price_daily_id) . '" class="d-inline">'
                    . csrf_field()
                    . '<button type="submit" class="btn btn-sm btn-warning me-1"><i class="bi bi-arrow-repeat"></i> Sync</button>'
                    . '</form>';

                $regen = '<form method="POST" action="' . route('admin.gold-price-daily.regen-ai', $row->gold_price_daily_id) . '" class="d-inline">'
                    . csrf_field()
                    . '<button type="submit" class="btn btn-sm btn-secondary"><i class="bi bi-robot"></i> AI</button>'
                    . '</form>';

                return $view . $sync . $regen;
            })
            ->rawColumns(['reason_from_ai', 'action'])
            ->orderColumn('gold_price_date', 'gold_price_date $1')
            ->make(true);
    }

    public function show(GoldPriceDaily $goldPriceDaily)
    {
        $intraday = GoldPrice::whereDate('last_updated', $goldPriceDaily->gold_price_date)
            ->orderBy('last_updated')
            ->get();

        return view('admin.gold-price-daily.view', compact('goldPriceDaily', 'intraday'));
    }

    public function manualSync(Request $request)
    {
        $request->validate([
            'sync_date' => 'required|date_format:Y-m-d',
        ]);

        $result = $this->service->syncDate($request->sync_date);

        $flashKey = $result['status'] === 'success' ? 'success' : ($result['status'] === 'no_data' ? 'error' : 'error');

        return redirect()->route('admin.gold-price-daily.index')
            ->with($flashKey, $result['message']);
    }

    public function syncThis(GoldPriceDaily $goldPriceDaily)
    {
        $date   = $goldPriceDaily->gold_price_date->toDateString();
        $result = $this->service->syncDate($date);

        $flashKey = $result['status'] === 'success' ? 'success' : 'error';

        return redirect()->route('admin.gold-price-daily.index')
            ->with($flashKey, $result['message']);
    }

    public function regenAi(GoldPriceDaily $goldPriceDaily)
    {
        $result   = $this->service->regenerateAiReason($goldPriceDaily);
        $flashKey = $result['status'] === 'success' ? 'success' : 'error';

        return redirect()->route('admin.gold-price-daily.index')
            ->with($flashKey, $result['message']);
    }

    private function buildStats(): array
    {
        $latest = GoldPriceDaily::orderByDesc('gold_price_date')->first();

        return [
            'total_records'       => GoldPriceDaily::count(),
            'last_sync_date'      => $latest?->gold_price_date?->format('d M Y') ?? '—',
            'latest_avg_buy'      => $latest?->buy_price      !== null ? number_format((float) $latest->buy_price,      2) : '—',
            'latest_avg_sell'     => $latest?->sell_price     !== null ? number_format((float) $latest->sell_price,     2) : '—',
            'highest_buy'         => GoldPriceDaily::max('highest_price') !== null ? number_format((float) GoldPriceDaily::max('highest_price'), 2) : '—',
            'lowest_buy'          => GoldPriceDaily::min('lowest_price')  !== null ? number_format((float) GoldPriceDaily::min('lowest_price'),  2) : '—',
            'total_with_ai'       => GoldPriceDaily::whereNotNull('reason_from_ai')->count(),
            'last_updated_at'     => $latest?->updated_at?->format('d M Y H:i') ?? '—',
        ];
    }
}
