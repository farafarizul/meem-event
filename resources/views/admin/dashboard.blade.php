<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-people fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_users']) }}</div>
                        <div class="text-muted small">Total Users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="bi bi-calendar2-event fs-4 text-success"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_events']) }}</div>
                        <div class="text-muted small">Total Events</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-check2-circle fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_checkins']) }}</div>
                        <div class="text-muted small">Total Check-ins</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                        <i class="bi bi-wifi fs-4 text-info"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_online_events']) }}</div>
                        <div class="text-muted small">Online Events</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="bi bi-geo-alt fs-4 text-danger"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_onsite_events']) }}</div>
                        <div class="text-muted small">Onsite Events</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Welcome + Recent Check-ins --}}
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted text-uppercase small fw-semibold">Administrator</h6>
                    <h5 class="mb-1">{{ Auth::user()->fullname }}</h5>
                    <p class="text-muted small mb-0">{{ Auth::user()->meem_code }}</p>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-people me-1"></i>Manage Users
                        </a>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-calendar2-event me-1"></i>Manage Events
                        </a>
                        <a href="{{ route('admin.checkins.index') }}" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-check2-circle me-1"></i>View Check-ins
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-1"></i>Recent Check-ins</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Meem Code</th>
                                    <th>Event</th>
                                    <th>Checked In At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recent_checkins as $checkin)
                                    <tr>
                                        <td>{{ $checkin->user?->fullname ?? '-' }}</td>
                                        <td><span class="badge bg-secondary">{{ $checkin->user?->meem_code ?? '-' }}</span></td>
                                        <td>{{ $checkin->event?->event_name ?? '-' }}</td>
                                        <td class="text-muted small">{{ $checkin->checked_in_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No check-ins yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
