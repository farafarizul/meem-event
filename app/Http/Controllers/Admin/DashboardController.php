<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCheckin;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'          => User::where('is_admin', false)->count(),
            'total_events'         => Event::count(),
            'total_checkins'       => EventCheckin::count(),
            'total_online_events'  => Event::where('category_event', 'online')->count(),
            'total_onsite_events'  => Event::where('category_event', 'onsite')->count(),
        ];

        $recent_checkins = EventCheckin::with(['user', 'event'])
            ->latest('checked_in_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_checkins'));
    }
}
