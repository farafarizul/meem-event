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
            ->when($eventId, fn ($q) => $q->where('event_id', $eventId))
            ->select('event_checkins.*');

        return DataTables::of($checkins)
            ->addIndexColumn()
            ->addColumn('meem_code', fn ($c) => $c->user ? $c->user->meem_code : '-')
            ->addColumn('fullname', fn ($c) => $c->user ? $c->user->fullname : '-')
            ->addColumn('phone_number', fn ($c) => $c->user ? $c->user->phone_number : '-')
            ->addColumn('event_name', fn ($c) => $c->event ? $c->event->event_name : '-')
            ->editColumn('checked_in_at', fn ($c) => $c->checked_in_at->format('d M Y H:i'))
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
