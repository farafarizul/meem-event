<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'category_event'    => 'onsite',
                'event_name'        => 'Meem Annual Conference 2026',
                'location'          => 'Kuala Lumpur Convention Centre, Malaysia',
                'start_date'        => '2026-04-15',
                'end_date'          => '2026-04-16',
                'unique_identifier' => 'EVENT-CONF2026KL',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'category_event'    => 'online',
                'event_name'        => 'Meem Digital Summit 2026',
                'location'          => 'Online – Zoom',
                'start_date'        => '2026-05-10',
                'end_date'          => '2026-05-10',
                'unique_identifier' => 'EVENT-DGTLSMT26',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'category_event'    => 'onsite',
                'event_name'        => 'Community Networking Night',
                'location'          => 'Petaling Jaya, Malaysia',
                'start_date'        => '2026-06-20',
                'end_date'          => '2026-06-20',
                'unique_identifier' => 'EVENT-NETNIGHT26',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ];

        Event::insert($events);
    }
}
