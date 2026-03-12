<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LogController extends Controller
{
    public function index()
    {
        $meemCodes  = DB::table('far_log')->distinct()->orderBy('meem_code')->pluck('meem_code')->filter()->values();
        $modules    = DB::table('far_log')->distinct()->orderBy('trail_module')->pluck('trail_module')->filter()->values();
        $methods    = DB::table('far_log')->distinct()->orderBy('trail_method')->pluck('trail_method')->filter()->values();
        $operations = DB::table('far_log')->distinct()->orderBy('trail_operation')->pluck('trail_operation')->filter()->values();

        return view('admin.logs.index', compact('meemCodes', 'modules', 'methods', 'operations'));
    }

    public function datatable(Request $request)
    {
        $query = FarLog::leftJoin('users', 'users.meem_code', '=', 'far_log.meem_code')
            ->select([
                'far_log.meem_code',
                'users.fullname',
                'far_log.log_category',
                'far_log.trail_module',
                'far_log.trail_method',
                'far_log.trail_operation',
                'far_log.log_data_json',
                'far_log.create_dttm',
            ])
            ->when($request->filled('meem_code'), function ($q) use ($request) {
                $q->where('far_log.meem_code', $request->meem_code);
            })
            ->when($request->filled('module'), function ($q) use ($request) {
                $q->where('far_log.trail_module', $request->module);
            })
            ->when($request->filled('method'), function ($q) use ($request) {
                $q->where('far_log.trail_method', $request->method);
            })
            ->when($request->filled('operation'), function ($q) use ($request) {
                $q->where('far_log.trail_operation', $request->operation);
            })
            ->orderBy('far_log.create_dttm', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('json_preview', function ($row) {
                $raw = $row->log_data_json ?? '';
                if ($raw === '' || $raw === null) {
                    return '<span class="text-muted">—</span>';
                }
                $preview = mb_strlen($raw) > 80 ? mb_substr($raw, 0, 80) . '…' : $raw;
                return '<span class="text-truncate d-inline-block" style="max-width:160px;" title="">'
                    . e($preview)
                    . '</span>'
                    . ' <button type="button" class="btn btn-xs btn-outline-secondary btn-view-json py-0 px-1 ms-1"'
                    . ' style="font-size:0.7rem;"'
                    . ' data-json="' . e($raw) . '">'
                    . '<i class="bi bi-braces"></i> View JSON</button>';
            })
            ->editColumn('fullname', function ($row) {
                return $row->fullname ?? '<span class="text-muted">—</span>';
            })
            ->rawColumns(['json_preview', 'fullname'])
            ->make(true);
    }
}
