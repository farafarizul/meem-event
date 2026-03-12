<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldPrice;
use App\Services\GoldPriceService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GoldPriceController extends Controller
{
    public function __construct(private GoldPriceService $service) {}

    public function index()
    {
        $latest = $this->service->getLatestRecord();

        return view('admin.gold-price.index', compact('latest'));
    }

    public function datatable(Request $request)
    {
        $query = GoldPrice::select([
            'gold_price_id',
            'type',
            'product',
            'unit',
            'currency',
            'sell_price',
            'buy_price',
            'timezone',
            'last_updated',
            'created_at',
        ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('sell_price', fn ($row) => number_format($row->sell_price, 2))
            ->editColumn('buy_price', fn ($row) => number_format($row->buy_price, 2))
            ->editColumn('last_updated', fn ($row) => $row->last_updated->format('d M Y H:i:s'))
            ->editColumn('created_at', fn ($row) => $row->created_at->format('d M Y H:i:s'))
            ->make(true);
    }

    public function syncNow()
    {
        $result = $this->service->forceSyncLatestGoldPrice();

        $message = $result['message'];

        return redirect()->route('admin.gold-price.index')
            ->with($result['status'] === 'failed' ? 'error' : 'success', $message);
    }
}
