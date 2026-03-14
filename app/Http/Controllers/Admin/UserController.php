<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
            ->where('status', 'active')
            ->select(['user_id', 'meem_code', 'meem_id', 'fullname', 'phone_number', 'email', 'created_at', 'profile_picture']);

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('profile_picture', function ($user) {
                if ($user->profile_picture) {
                    $url = str_starts_with($user->profile_picture, 'http')
                        ? $user->profile_picture
                        : asset('storage/' . $user->profile_picture);
                    return '<img src="' . e($url) . '" alt="Photo"'
                        . ' class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">';
                }
                return '<i class="bi bi-person-circle fs-4 text-secondary"></i>';
            })
            ->addColumn('action', function ($user) {
                $edit = '<button class="btn btn-sm btn-warning btn-edit me-1"'
                    . ' data-id="' . $user->user_id . '"'
                    . ' data-meemcode="' . e($user->meem_code) . '"'
                    . ' data-meemid="' . e($user->meem_id ?? '') . '"'
                    . ' data-fullname="' . e($user->fullname) . '"'
                    . ' data-phone="' . e($user->phone_number) . '"'
                    . ' data-email="' . e($user->email ?? '') . '">'
                    . '<i class="bi bi-pencil-fill"></i> Edit</button>';
                $del = '<button class="btn btn-sm btn-danger btn-delete"'
                    . ' data-id="' . $user->user_id . '"'
                    . ' data-name="' . e($user->fullname) . '">';
                    //. '<i class="bi bi-trash-fill"></i> Delete</button>';
                return $edit . $del;
            })
            ->editColumn('created_at', fn ($u) => $u->created_at->format('d M Y'))
            ->rawColumns(['profile_picture', 'action'])
            ->make(true);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'meem_code'    => 'required|string|max:50|unique:users,meem_code,' . $user->user_id . ',user_id',
            'meem_id'      => 'nullable|string|max:100',
            'fullname'     => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email'        => 'nullable|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
        ]);

        $user->update($validated);

        return response()->json(['success' => true, 'message' => 'User updated successfully.']);
    }

    public function destroy(User $user)
    {
        $user->update(['status' => 'deleted']);

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }

    public function export(Request $request)
    {
        $search   = $request->get('search');
        $filename = 'users_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new UserExport($search), $filename);
    }
}
