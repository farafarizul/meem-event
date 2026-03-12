<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    {{-- Stats Cards --}}
    <div class="nk-block nk-block-lg">
        <div class="row g-gs">
            <div class="col-xxl-4 col-sm-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-0">
                            <div class="card-title">
                                <h6 class="subtitle">Total Users</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-users text-primary" style="font-size:1.5rem;"></em>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount">{{ number_format($stats['total_users']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-sm-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-0">
                            <div class="card-title">
                                <h6 class="subtitle">Total Events</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-calendar text-success" style="font-size:1.5rem;"></em>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount">{{ number_format($stats['total_events']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-sm-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-0">
                            <div class="card-title">
                                <h6 class="subtitle">Total Check-ins</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-check-circle text-warning" style="font-size:1.5rem;"></em>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount">{{ number_format($stats['total_checkins']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-sm-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-0">
                            <div class="card-title">
                                <h6 class="subtitle">Online Events</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-wifi text-info" style="font-size:1.5rem;"></em>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount">{{ number_format($stats['total_online_events']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-sm-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-0">
                            <div class="card-title">
                                <h6 class="subtitle">Onsite Events</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-map-pin text-danger" style="font-size:1.5rem;"></em>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount">{{ number_format($stats['total_onsite_events']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Welcome + Recent Check-ins --}}
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-md-4">
                <div class="card card-bordered h-100">
                    <div class="card-inner">
                        <div class="card-title-group mb-3">
                            <div class="card-title">
                                <h6 class="title">Administrator</h6>
                            </div>
                        </div>
                        <h5 class="mb-1">{{ Auth::user()->fullname }}</h5>
                        <p class="text-soft mb-3">{{ Auth::user()->meem_code }}</p>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                                <em class="icon ni ni-users me-1"></em>Manage Users
                            </a>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-success btn-sm">
                                <em class="icon ni ni-calendar me-1"></em>Manage Events
                            </a>
                            <a href="{{ route('admin.checkins.index') }}" class="btn btn-outline-warning btn-sm">
                                <em class="icon ni ni-check-circle me-1"></em>View Check-ins
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title"><em class="icon ni ni-clock me-1"></em>Recent Check-ins</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner p-0">
                            <div class="table-responsive">
                                <table class="table table-orders">
                                    <thead class="tb-odr-head">
                                        <tr class="tb-odr-item">
                                            <th class="tb-odr-info"><span class="tb-odr-desc d-none d-sm-inline-block">User</span></th>
                                            <th class="tb-odr-info"><span>Meem Code</span></th>
                                            <th class="tb-odr-info"><span class="d-none d-md-inline-block">Event</span></th>
                                            <th class="tb-odr-info"><span class="d-none d-md-inline-block">Checked In At</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="tb-odr-body">
                                        @forelse ($recent_checkins as $checkin)
                                            <tr class="tb-odr-item">
                                                <td class="tb-odr-info">{{ $checkin->user?->fullname ?? '-' }}</td>
                                                <td class="tb-odr-info">
                                                    <span class="badge badge-sm bg-outline-secondary">{{ $checkin->user?->meem_code ?? '-' }}</span>
                                                </td>
                                                <td class="tb-odr-info d-none d-md-table-cell">{{ $checkin->event?->event_name ?? '-' }}</td>
                                                <td class="tb-odr-info d-none d-md-table-cell text-soft">{{ $checkin->checked_in_at->format('d M Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-soft py-3">No check-ins yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
