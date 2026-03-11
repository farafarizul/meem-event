<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EventExport;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class EventController extends Controller
{
    public function index()
    {
        return view('admin.events.index');
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
        $events = Event::select(['id', 'unique_identifier', 'event_name', 'category_event', 'location', 'start_date', 'end_date']);

        return DataTables::of($events)
            ->addIndexColumn()
            ->editColumn('start_date', fn ($e) => $e->start_date->format('d M Y'))
            ->editColumn('end_date', fn ($e) => $e->end_date->format('d M Y'))
            ->editColumn('category_event', fn ($e) => '<span class="badge bg-' . ($e->category_event === 'online' ? 'info' : 'success') . '">' . ucfirst($e->category_event) . '</span>')
            ->addColumn('action', function ($event) {
                $edit = '<a href="' . route('admin.events.edit', $event->id) . '" class="btn btn-sm btn-warning me-1">'
                    . '<i class="bi bi-pencil-fill"></i> Edit</a>';
                $del = '<button class="btn btn-sm btn-danger btn-delete"'
                    . ' data-id="' . $event->id . '"'
                    . ' data-name="' . e($event->event_name) . '">'
                    . '<i class="bi bi-trash-fill"></i> Delete</button>';
                return $edit . $del;
            })
            ->rawColumns(['category_event', 'action'])
            ->make(true);
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'category_event'    => 'required|in:online,onsite',
            'event_name'        => 'required|string|max:255',
            'location'          => 'required|string|max:255',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'unique_identifier' => ['required', 'string', 'max:16', 'unique:events,unique_identifier,' . $event->id, 'regex:/^EVENT-[A-Z0-9]{10}$/'],
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
