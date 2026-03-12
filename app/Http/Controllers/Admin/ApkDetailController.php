<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApkDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ApkDetailController extends Controller
{
    public function index()
    {
        return view('admin.apk-detail.index');
    }

    public function create()
    {
        return view('admin.apk-detail.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'apk_file'    => 'required|file|max:204800',
            'description' => 'required|string|max:2000',
        ]);

        $file = $request->file('apk_file');

        if (strtolower($file->getClientOriginalExtension()) !== 'apk') {
            return back()
                ->withErrors(['apk_file' => 'Only .apk files are allowed.'])
                ->withInput();
        }

        $originalFilename = $file->getClientOriginalName();
        $now              = now();
        $newFilename      = $now->format('Y-m-d') . '-' . $now->timestamp . '.apk';

        $file->storeAs('apks', $newFilename, 'public');

        $apkDetail = ApkDetail::create([
            'original_filename' => $originalFilename,
            'new_filename'      => $newFilename,
            'uploaded_date'     => $now->toDateString(),
            'description'       => $request->description,
            'download_link'     => '',
        ]);

        $apkDetail->update([
            'download_link' => route('apk.download', $apkDetail->apk_detail_id),
        ]);

        return redirect()->route('admin.apk-detail.index')
            ->with('success', 'APK file uploaded successfully.');
    }

    public function datatable(Request $request)
    {
        $apks = ApkDetail::select([
            'apk_detail_id',
            'original_filename',
            'new_filename',
            'uploaded_date',
            'description',
            'download_link',
        ]);

        return DataTables::of($apks)
            ->addIndexColumn()
            ->editColumn('uploaded_date', fn ($a) => $a->uploaded_date->format('d M Y'))
            ->editColumn('download_link', fn ($a) => '<a href="' . e($a->download_link) . '" target="_blank" class="text-break">' . e($a->download_link) . '</a>')
            ->addColumn('action', function ($apk) {
                $download = '<a href="' . e($apk->download_link) . '" class="btn btn-sm btn-success me-1">'
                    . '<i class="bi bi-download"></i> Download</a>';
                $del = '<button class="btn btn-sm btn-danger btn-delete"'
                    . ' data-id="' . $apk->apk_detail_id . '"'
                    . ' data-name="' . e($apk->original_filename) . '">'
                    . '<i class="bi bi-trash-fill"></i> Delete</button>';

                return $download . $del;
            })
            ->rawColumns(['download_link', 'action'])
            ->make(true);
    }

    public function destroy(ApkDetail $apkDetail)
    {
        Storage::disk('public')->delete('apks/' . $apkDetail->new_filename);
        $apkDetail->delete();

        return response()->json(['success' => true, 'message' => 'APK deleted successfully.']);
    }

    public function download(ApkDetail $apkDetail)
    {
        $path = 'apks/' . $apkDetail->new_filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($path, $apkDetail->original_filename);
    }
}
