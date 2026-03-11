<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCheckin;
use App\Models\User;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    /** Obfuscation offset applied to user IDs embedded in QR codes. */
    private const USER_ID_OFFSET = 14254;

    /**
     * Decode the obfuscated user_id and resolve both the Event and User models.
     * Returns an associative array with 'event', 'user', and 'actualUserId',
     * or null if any parameter is invalid / records not found.
     *
     * @param  string      $scannedfromapp   Event unique identifier from the QR URL
     * @param  string|int  $obfuscatedUserId Raw user_id value from the QR URL
     * @return array{event: \App\Models\Event, user: \App\Models\User, actualUserId: int}|null
     */
    private function decodeAndResolve(string $scannedfromapp, $obfuscatedUserId): ?array
    {
        if (!ctype_digit((string) $obfuscatedUserId)) {
            return null;
        }

        $actualUserId = (int) $obfuscatedUserId - self::USER_ID_OFFSET;
        if ($actualUserId <= 0) {
            return null;
        }

        $event = Event::where('unique_identifier', $scannedfromapp)->first();
        if (!$event) {
            return null;
        }



        $user = User::where('meem_id', $actualUserId)->where('is_admin', false)->first();
        if (!$user) {
            return null;
        }

        return compact('event', 'user', 'actualUserId');
    }

    // ── QR code scan flow ────────────────────────────────────────────────────

    /**
     * Show the check-in confirmation page for a user scanned via QR code.
     * URL: /checkin?scannedfromapp=EVENT-XXX&user_id=OBFUSCATED
     */
    public function showByQR(Request $request)
    {
        $scannedfromapp   = (string) $request->query('scannedfromapp', '');
        $obfuscatedUserId = (string) $request->query('user_id', '');



        $resolved = $this->decodeAndResolve($scannedfromapp, $obfuscatedUserId);


        if (!$resolved) {
            return view('checkin.invalid');
        }

        ['event' => $event, 'user' => $user, 'actualUserId' => $actualUserId] = $resolved;

        $alreadyCheckedIn = EventCheckin::where('event_id', $event->event_id)
            ->where('user_id', $actualUserId)
            ->exists();

        if ($alreadyCheckedIn) {
            return view('checkin.already', compact('event', 'user'));
        }

        return view('checkin.show', [
            'event'            => $event,
            'user'             => $user,
            'scannedfromapp'   => $scannedfromapp,
            'obfuscatedUserId' => $obfuscatedUserId,
        ]);
    }

    /**
     * Store a check-in submitted via the QR code flow.
     * POST /checkin (hidden fields: scannedfromapp, user_id)
     */
    public function storeByQR(Request $request)
    {
        $request->validate([
            'scannedfromapp' => ['required', 'string'],
            'user_id'        => ['required', 'numeric', 'min:1'],
        ]);

        $scannedfromapp   = $request->input('scannedfromapp');
        $obfuscatedUserId = (string) $request->input('user_id');

        $resolved = $this->decodeAndResolve($scannedfromapp, $obfuscatedUserId);

        if (!$resolved) {
            return view('checkin.invalid');
        }



        ['event' => $event, 'user' => $user, 'actualUserId' => $actualUserId] = $resolved;

        $user = User::where('meem_id', $actualUserId)->where('is_admin', false)->first();


        // Guard: only one check-in per user per event (backend duplicate prevention)
        $alreadyCheckedIn = EventCheckin::where('event_id', $event->event_id)
            ->where('user_id', $actualUserId)
            ->exists();

        if ($alreadyCheckedIn) {
            return view('checkin.already', compact('event', 'user'));
        }



        $checkin = EventCheckin::create([
            'event_id'      => $event->id,
            'user_id'       => $user->id,
            'checked_in_at' => now(),
        ]);

        return view('checkin.success', compact('event', 'user', 'checkin'));
    }

    // ── Legacy list-based flow ───────────────────────────────────────────────

    public function show(string $uniqueIdentifier)
    {
        $event = Event::where('unique_identifier', $uniqueIdentifier)->firstOrFail();
        $users = User::where('is_admin', false)->orderBy('meem_code')->get(['user_id', 'meem_code', 'fullname', 'phone_number']);

        return view('checkin.show', compact('event', 'users'));
    }

    public function store(Request $request, string $uniqueIdentifier)
    {
        $event = Event::where('unique_identifier', $uniqueIdentifier)->firstOrFail();

        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
        ]);

        $userId = (int) $request->input('user_id');

        // Guard: only one check-in per user per event
        $alreadyCheckedIn = EventCheckin::where('event_id', $event->event_id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyCheckedIn) {
            $user = User::findOrFail($userId);
            return view('checkin.already', compact('event', 'user'));
        }

        $checkin = EventCheckin::create([
            'event_id'      => $event->event_id,
            'user_id'       => $userId,
            'checked_in_at' => now(),
        ]);

        $user = User::findOrFail($userId);

        return view('checkin.success', compact('event', 'user', 'checkin'));
    }
}
