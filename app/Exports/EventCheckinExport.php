<?php

namespace App\Exports;

use App\Models\EventCheckin;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventCheckinExport implements FromQuery, WithHeadings, WithMapping
{
    private ?int $eventId;
    private ?string $search;

    public function __construct(?int $eventId = null, ?string $search = null)
    {
        $this->eventId = $eventId;
        $this->search  = $search;
    }

    public function query()
    {
        $q = EventCheckin::with(['user', 'event']);

        if ($this->eventId) {
            $q->where('event_id', $this->eventId);
        }

        if ($this->search) {
            $term = $this->search;
            $q->whereHas('user', function ($query) use ($term) {
                $query->where('fullname', 'like', "%{$term}%")
                    ->orWhere('meem_code', 'like', "%{$term}%")
                    ->orWhere('phone_number', 'like', "%{$term}%");
            });
        }

        return $q;
    }

    public function headings(): array
    {
        return ['#', 'Meem Code', 'Full Name', 'Phone Number', 'Event', 'Checked In At'];
    }

    public function map($checkin): array
    {
        return [
            $checkin->id,
            $checkin->user ? $checkin->user->meem_code : '-',
            $checkin->user ? $checkin->user->fullname : '-',
            $checkin->user ? $checkin->user->phone_number : '-',
            $checkin->event ? $checkin->event->event_name : '-',
            $checkin->checked_in_at->format('Y-m-d H:i:s'),
        ];
    }
}
