<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SilverPrice;
use App\Services\SilverPriceService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SilverPriceController extends Controller
{
    public function __construct(private SilverPriceService $service) {}

    public function index()
    {
        $latest = $this->service->getLatestRecord();

        return view('admin.silver-price.index', compact('latest'));
    }

    public function datatable(Request $request)
    {
        $query = SilverPrice::select([
            'silver_price_id',
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
        $result = $this->service->forceSyncLatestSilverPrice();

        $message = $result['message'];

        return redirect()->route('admin.silver-price.index')
            ->with($result['status'] === 'failed' ? 'error' : 'success', $message);
    }
}
