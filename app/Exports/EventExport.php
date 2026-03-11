<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventExport implements FromQuery, WithHeadings, WithMapping
{
    private ?string $search;

    public function __construct(?string $search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        $q = Event::query();

        if ($this->search) {
            $term = $this->search;
            $q->where(function ($query) use ($term) {
                $query->where('event_name', 'like', "%{$term}%")
                    ->orWhere('unique_identifier', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%")
                    ->orWhere('category_event', 'like', "%{$term}%");
            });
        }

        return $q;
    }

    public function headings(): array
    {
        return ['#', 'Unique ID', 'Event Name', 'Category', 'Location', 'Start Date', 'End Date'];
    }

    public function map($event): array
    {
        return [
            $event->event_id,
            $event->unique_identifier,
            $event->event_name,
            ucfirst($event->category_event),
            $event->location,
            $event->start_date->format('Y-m-d'),
            $event->end_date->format('Y-m-d'),
        ];
    }
}
