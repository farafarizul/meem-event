<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListCountry;
use App\Services\CountrySyncService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ListCountryController extends Controller
{
    public function __construct(private CountrySyncService $service) {}

    public function index()
    {
        return view('admin.settings.list-countries.index');
    }

    public function datatable(Request $request)
    {
        $query = ListCountry::select(['country_id', 'id', 'name', 'created_at', 'updated_at']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('created_at', fn ($row) => $row->created_at->format('d M Y H:i:s'))
            ->editColumn('updated_at', fn ($row) => $row->updated_at->format('d M Y H:i:s'))
            ->make(true);
    }

    public function sync()
    {
        $result = $this->service->sync();

        return response()->json([
            'status'   => $result['status'],
            'inserted' => $result['inserted'],
            'updated'  => $result['updated'],
            'deleted'  => $result['deleted'],
        ]);
    }
}
