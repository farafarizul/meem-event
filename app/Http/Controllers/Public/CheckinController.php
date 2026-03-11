<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCheckin;
use App\Models\User;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function show(string $uniqueIdentifier)
    {
        $event = Event::where('unique_identifier', $uniqueIdentifier)->firstOrFail();
        $users = User::where('is_admin', false)->orderBy('meem_code')->get(['id', 'meem_code', 'fullname', 'phone_number']);

        return view('checkin.show', compact('event', 'users'));
    }

    public function store(Request $request, string $uniqueIdentifier)
    {
        $event = Event::where('unique_identifier', $uniqueIdentifier)->firstOrFail();

        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $userId = (int) $request->input('user_id');

        // Guard: only one check-in per user per event
        $alreadyCheckedIn = EventCheckin::where('event_id', $event->id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyCheckedIn) {
            $user = User::findOrFail($userId);
            return view('checkin.already', compact('event', 'user'));
        }

        $checkin = EventCheckin::create([
            'event_id'      => $event->id,
            'user_id'       => $userId,
            'checked_in_at' => now(),
        ]);

        $user = User::findOrFail($userId);

        return view('checkin.success', compact('event', 'user', 'checkin'));
    }
}
