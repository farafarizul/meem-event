<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EventExport;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Event;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class EventController extends Controller
{
    public function show(Event $event)
    {
        $checkinUrl = route('checkin.show', $event->unique_identifier);
        $qrSvg      = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($event->unique_identifier);

        return view('admin.events.show', compact('event', 'qrSvg', 'checkinUrl'));
    }

    public function qrDownload(Event $event)
    {
        $checkinUrl = route('checkin.show', $event->unique_identifier);
        $qrPng      = QrCode::format('png')
            ->size(1200)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($event->unique_identifier);

        $filename = 'qr_' . $event->unique_identifier . '.png';

        return response($qrPng, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function index()
    {
        return view('admin.events.index');
    }

    public function create()
    {
        $branches = Branch::orderBy('branch_name')->get();
        return view('admin.events.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id'         => 'nullable|exists:branches,branch_id',
            'category_event'    => 'required|in:online,onsite',
            'event_name'        => 'required|string|max:255',
            'location'          => 'required|string|max:255',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'unique_identifier' => ['required', 'string', 'max:16', 'unique:events,unique_identifier', 'regex:/^EVENT-[A-Z0-9]{10}$/'],
        ]);

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function datatable(Request $request)
    {
        $events = Event::select(['event_id', 'unique_identifier', 'event_name', 'category_event', 'location', 'start_date', 'end_date']);

        return DataTables::of($events)
            ->addIndexColumn()
            ->editColumn('start_date', fn ($e) => $e->start_date->format('d M Y'))
            ->editColumn('end_date', fn ($e) => $e->end_date->format('d M Y'))
            ->editColumn('category_event', fn ($e) => '<span class="badge bg-' . ($e->category_event === 'online' ? 'info' : 'success') . '">' . ucfirst($e->category_event) . '</span>')
            ->addColumn('action', function ($event) {
                $view = '<a href="' . route('admin.events.show', $event->event_id) . '" class="btn btn-sm btn-info me-1 text-white">'
                    . '<i class="bi bi-eye-fill"></i> View</a>';
                $edit = '<a href="' . route('admin.events.edit', $event->event_id) . '" class="btn btn-sm btn-warning me-1">'
                    . '<i class="bi bi-pencil-fill"></i> Edit</a>';
                $del = '<button class="btn btn-sm btn-danger btn-delete"'
                    . ' data-id="' . $event->event_id . '"'
                    . ' data-name="' . e($event->event_name) . '">'
                    . '<i class="bi bi-trash-fill"></i> Delete</button>';
                return $view . $edit . $del;
            })
            ->rawColumns(['category_event', 'action'])
            ->make(true);
    }

    public function edit(Event $event)
    {
        $branches = Branch::orderBy('branch_name')->get();
        return view('admin.events.edit', compact('event', 'branches'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'branch_id'         => 'nullable|exists:branches,branch_id',
            'category_event'    => 'required|in:online,onsite',
            'event_name'        => 'required|string|max:255',
            'location'          => 'required|string|max:255',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'unique_identifier' => ['required', 'string', 'max:16', 'unique:events,unique_identifier,' . $event->event_id . ',event_id', 'regex:/^EVENT-[A-Z0-9]{10}$/'],
        ]);

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json(['success' => true, 'message' => 'Event deleted successfully.']);
    }

    public function export(Request $request)
    {
        $search   = $request->get('search');
        $filename = 'events_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new EventExport($search), $filename);
    }
}
