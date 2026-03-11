<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function datatable(Request $request)
    {
        $users = User::where('is_admin', false)
            ->select(['id', 'meem_code', 'fullname', 'phone_number', 'email', 'created_at']);

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('action', function ($user) {
                $edit = '<button class="btn btn-sm btn-warning btn-edit me-1"'
                    . ' data-id="' . $user->id . '"'
                    . ' data-meemcode="' . e($user->meem_code) . '"'
                    . ' data-fullname="' . e($user->fullname) . '"'
                    . ' data-phone="' . e($user->phone_number) . '"'
                    . ' data-email="' . e($user->email ?? '') . '">'
                    . '<i class="bi bi-pencil-fill"></i> Edit</button>';
                $del = '<button class="btn btn-sm btn-danger btn-delete"'
                    . ' data-id="' . $user->id . '"'
                    . ' data-name="' . e($user->fullname) . '">'
                    . '<i class="bi bi-trash-fill"></i> Delete</button>';
                return $edit . $del;
            })
            ->editColumn('created_at', fn ($u) => $u->created_at->format('d M Y'))
            ->rawColumns(['action'])
            ->make(true);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'id'           => 'required|integer|min:1|unique:users,id,' . $user->id,
            'meem_code'    => 'required|string|max:50|unique:users,meem_code,' . $user->id,
            'fullname'     => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email'        => 'nullable|email|max:255|unique:users,email,' . $user->id,
        ]);

        $newId = (int) $validated['id'];
        unset($validated['id']);

        $user->update($validated);

        if ($newId !== $user->id) {
            DB::table('users')->where('id', $user->id)->update(['id' => $newId]);
        }

        return response()->json(['success' => true, 'message' => 'User updated successfully.']);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }

    public function export(Request $request)
    {
        $search   = $request->get('search');
        $filename = 'users_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new UserExport($search), $filename);
    }
}
