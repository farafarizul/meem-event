<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EventCheckinExport;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCheckin;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class EventCheckinController extends Controller
{
    public function index(Request $request)
    {
        $events          = Event::orderBy('event_name')->get(['event_id', 'event_name', 'unique_identifier']);
        $selectedEventId = $request->get('event_id');

        return view('admin.checkins.index', compact('events', 'selectedEventId'));
    }

    public function datatable(Request $request)
    {
        $eventId = $request->get('event_id');

        $checkins = EventCheckin::with(['user', 'event'])

            ->leftJoin('users', 'users.user_id', '=', 'event_checkins.user_id')
            ->leftJoin('events', 'events.event_id', '=', 'event_checkins.event_id')
            ->select([
                'event_checkins.event_checkin_id',
                'event_checkins.checked_in_at',
                'users.fullname as user_fullname',
                'events.event_name as event_name',
                'users.user_id as user_id',
                'users.meem_code as meem_code',
                'users.fullname as fullname',
                'users.phone_number as phone_number',
            ])
            ->when($eventId, function ($query) use ($eventId) {
                $query->where('event_checkins.event_id', $eventId);
            })
            ->orderBy('event_checkins.checked_in_at', 'desc');

        //print_r($checkins->toSql()); // Debug: Output the generated SQL query
        // exit();

        return DataTables::of($checkins)
            ->addIndexColumn()
            ->addColumn('action', function ($checkin) {
                return '<button class="btn btn-sm btn-danger btn-delete"'
                    . ' data-id="' . $checkin->event_checkin_id . '"'
                    . ' data-name="' . e($checkin->user ? $checkin->user->fullname : '') . '">'
                    . '<i class="bi bi-trash-fill"></i> Delete</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function destroy(EventCheckin $checkin)
    {
        $checkin->delete();

        return response()->json(['success' => true, 'message' => 'Check-in record deleted successfully.']);
    }

    public function export(Request $request)
    {
        $eventId  = $request->get('event_id');
        $search   = $request->get('search');
        $filename = 'checkins_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new EventCheckinExport($eventId, $search), $filename);
    }
}
