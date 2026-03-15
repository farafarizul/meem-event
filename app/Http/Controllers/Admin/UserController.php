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
            ->where('status', 'active')
            ->select(['user_id', 'meem_code', 'meem_id', 'fullname', 'phone_number', 'email', 'created_at', 'profile_picture', 'device_name']);

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
                $detailUrl = route('admin.users.edit', $user->user_id);
                return '<a href="' . $detailUrl . '" class="btn btn-sm btn-primary">'
                    . '<i class="bi bi-eye-fill me-1"></i>Detail</a>';
            })
            ->editColumn('created_at', fn ($u) => $u->created_at->format('d M Y'))
            ->rawColumns(['profile_picture', 'action'])
            ->make(true);
    }

    public function edit(User $user)
    {
        $logData      = $this->decodeLatestProfileLog($user->meem_code);
        $introducer   = $logData['introducer'] ?? null;
        $gssDetail    = $logData['gss_detail'] ?? null;
        $sssDetail    = $logData['sss_silver_detail'] ?? null;

        $sessionStats = DB::table('far_log')
            ->where('meem_code', $user->meem_code)
            ->selectRaw('COUNT(*) as total_logs, COUNT(DISTINCT app_session) as distinct_sessions')
            ->first();

        $latestSession = DB::table('far_log')
            ->where('meem_code', $user->meem_code)
            ->whereNotNull('app_session')
            ->orderBy('create_dttm', 'desc')
            ->value('app_session');

        return view('admin.users.edit', compact(
            'user', 'introducer', 'gssDetail', 'sssDetail', 'sessionStats', 'latestSession'
        ));
    }

    public function tabBasicInfo(User $user)
    {
        $logData = $this->decodeLatestProfileLog($user->meem_code);

        return view('admin.users.partials.tab-basic-info', compact('user', 'logData'));
    }

    public function tabEvents(User $user)
    {
        $userId = $user->user_id;

        $totalDistinctEvents = DB::table('event_checkins')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->distinct('event_id')
            ->count('event_id');

        $totalCheckins = DB::table('event_checkins')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->count();

        $checkins = DB::table('event_checkins')
            ->leftJoin('events', 'events.event_id', '=', 'event_checkins.event_id')
            ->where('event_checkins.user_id', $userId)
            ->whereNull('event_checkins.deleted_at')
            ->select([
                'event_checkins.event_checkin_id',
                'event_checkins.event_id',
                'event_checkins.checked_in_at',
                'event_checkins.status',
                'event_checkins.created_at',
                'events.event_name',
            ])
            ->orderBy('event_checkins.checked_in_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.users.partials.tab-events', compact(
            'user', 'totalDistinctEvents', 'totalCheckins', 'checkins'
        ));
    }

    public function tabLogs(User $user, Request $request)
    {
        $meemCode = $user->meem_code;

        $totalLogs = DB::table('far_log')
            ->where('meem_code', $meemCode)
            ->count();

        $distinctSessions = DB::table('far_log')
            ->where('meem_code', $meemCode)
            ->whereNotNull('app_session')
            ->distinct()
            ->orderBy('app_session')
            ->pluck('app_session');

        $totalDistinctSessions = $distinctSessions->count();

        $selectedSession = $request->get('app_session', '');

        $logs = DB::table('far_log')
            ->where('meem_code', $meemCode)
            ->when($selectedSession !== '', fn ($q) => $q->where('app_session', $selectedSession))
            ->orderBy('create_dttm', 'desc')
            ->limit(50)
            ->get();

        return view('admin.users.partials.tab-logs', compact(
            'user', 'totalLogs', 'totalDistinctSessions', 'distinctSessions', 'selectedSession', 'logs'
        ));
    }

    private function getLatestProfileLog(?string $meemCode)
    {
        if (!$meemCode) {
            return null;
        }

        return DB::table('far_log')
            ->where('meem_code', $meemCode)
            ->where('trail_module', 'api')
            ->where('trail_method', 'customer')
            ->where('trail_operation', 'profile')
            ->orderBy('create_dttm', 'desc')
            ->first();
    }

    private function decodeLatestProfileLog(?string $meemCode): array
    {
        $log = $this->getLatestProfileLog($meemCode);

        if (!$log || empty($log->log_data_json)) {
            return [];
        }

        $decoded = json_decode($log->log_data_json, true);

        return is_array($decoded) ? $decoded : [];
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
