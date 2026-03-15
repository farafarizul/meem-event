{{-- Tab 2: Events --}}

{{-- Stats cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-primary text-white text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold">{{ $totalDistinctEvents }}</div>
                <div class="small">Event Disertai</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-success text-white text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold">{{ $totalCheckins }}</div>
                <div class="small">Jumlah Check-in</div>
            </div>
        </div>
    </div>
</div>

{{-- Checkins table --}}
<div class="table-responsive">
    @if ($checkins->isNotEmpty())
        <table class="table table-hover align-middle table-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nama Event</th>
                    <th>Check-in</th>
                    <th>Status</th>
                    <th>Dicipta</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($checkins as $i => $checkin)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            @if ($checkin->event_name)
                                {{ $checkin->event_name }}
                            @else
                                <span class="text-muted">—</span>
                                @if ($checkin->event_id)
                                    <small class="text-muted">(ID: {{ $checkin->event_id }})</small>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($checkin->checked_in_at)
                                {{ \Carbon\Carbon::parse($checkin->checked_in_at)->format('d M Y, H:i') }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $checkin->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($checkin->status ?? '—') }}
                            </span>
                        </td>
                        <td>
                            @if ($checkin->created_at)
                                {{ \Carbon\Carbon::parse($checkin->created_at)->format('d M Y') }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($checkins->count() >= 50)
            <p class="text-muted small mt-2">
                <i class="bi bi-info-circle me-1"></i>Menunjukkan 50 rekod terkini.
            </p>
        @endif
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
            Tiada rekod check-in untuk pengguna ini.
        </div>
    @endif
</div>
