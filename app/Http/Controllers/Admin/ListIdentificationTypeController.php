<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListIdentificationType;
use App\Services\IdentificationTypeSyncService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ListIdentificationTypeController extends Controller
{
    public function __construct(private IdentificationTypeSyncService $service) {}

    public function index()
    {
        return view('admin.settings.list-identification-types.index');
    }

    public function datatable(Request $request)
    {
        $query = ListIdentificationType::select(['identification_type_id', 'id', 'name', 'created_at', 'updated_at']);

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
