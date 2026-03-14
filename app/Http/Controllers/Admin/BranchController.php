<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    public function index()
    {
        return view('admin.branches.index');
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_name'            => 'required|string|max:255',
            'branch_code'            => 'required|string|max:50|unique:branches,branch_code',
            'branch_phone'           => 'nullable|string|max:20',
            'branch_address'         => 'nullable|string|max:255',
            'postcode'               => 'nullable|string|max:10',
            'state'                  => 'nullable|string|max:100',
            'area'                   => 'nullable|string|max:100',
            'person_in_charge_name'  => 'nullable|string|max:255',
            'person_in_charge_phone' => 'nullable|string|max:20',
            'branch_type'            => 'required|in:Branch,HQ',
        ]);

        Branch::create($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function datatable(Request $request)
    {
        $branches = Branch::select(['branch_id', 'branch_name', 'branch_code', 'branch_phone', 'state', 'branch_type']);

        return DataTables::of($branches)
            ->addIndexColumn()
            ->editColumn('branch_type', fn ($b) => '<span class="badge bg-' . ($b->branch_type === 'HQ' ? 'danger' : 'primary') . '">' . e($b->branch_type) . '</span>')
            ->addColumn('action', function ($branch) {
                $edit = '<a href="' . route('admin.branches.edit', $branch->branch_id) . '" class="btn btn-sm btn-warning me-1">'
                    . '<i class="bi bi-pencil-fill"></i> Edit</a>';
                $del = '<button class="btn btn-sm btn-danger btn-delete"'
                    . ' data-id="' . $branch->branch_id . '"'
                    . ' data-name="' . e($branch->branch_name) . '">'
                    . '<i class="bi bi-trash-fill"></i> Delete</button>';
                return $edit . $del;
            })
            ->rawColumns(['branch_type', 'action'])
            ->make(true);
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'branch_name'            => 'required|string|max:255',
            'branch_code'            => 'required|string|max:50|unique:branches,branch_code,' . $branch->branch_id . ',branch_id',
            'branch_phone'           => 'nullable|string|max:20',
            'branch_address'         => 'nullable|string|max:255',
            'postcode'               => 'nullable|string|max:10',
            'state'                  => 'nullable|string|max:100',
            'area'                   => 'nullable|string|max:100',
            'person_in_charge_name'  => 'nullable|string|max:255',
            'person_in_charge_phone' => 'nullable|string|max:20',
            'branch_type'            => 'required|in:Branch,HQ',
        ]);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        //update status with deleted

        $branch->update(['status' => 'deleted']);
        $branch->delete();





        return response()->json(['success' => true, 'message' => 'Branch deleted successfully.']);
    }
}
