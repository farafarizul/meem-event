<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    {{-- ======================================================
         SECTION A — SUMMARY CARDS
    ====================================================== --}}
    <div class="row g-3 mb-4">

        {{-- Total Users --}}
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

        {{-- Total Events --}}
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

        {{-- Total Check-ins --}}
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

        {{-- Latest Gold Sell Price --}}
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-graph-up-arrow fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-6 fw-bold text-warning">
                            @if($stats['gold_sell_price'] !== null)
                                MYR {{ number_format($stats['gold_sell_price'], 2) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        <div class="text-muted small">Gold Sell Price</div>
                        @if($stats['gold_buy_price'] !== null)
                            <div class="small text-muted">Buy: MYR {{ number_format($stats['gold_buy_price'], 2) }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Latest Silver Sell Price --}}
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                        <i class="bi bi-coin fs-4 text-secondary"></i>
                    </div>
                    <div>
                        <div class="fs-6 fw-bold text-secondary">
                            @if($stats['silver_sell_price'] !== null)
                                MYR {{ number_format($stats['silver_sell_price'], 2) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        <div class="text-muted small">Silver Sell Price</div>
                        @if($stats['silver_buy_price'] !== null)
                            <div class="small text-muted">Buy: MYR {{ number_format($stats['silver_buy_price'], 2) }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ======================================================
         SECTION B — CHARTS
    ====================================================== --}}

    {{-- Monthly Line Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-graph-up-arrow me-1 text-warning"></i>Monthly Gold Price</h6>
                </div>
                <div class="card-body">
                    @if($goldChartLabels->isEmpty())
                        <p class="text-center text-muted py-4">No gold price data available.</p>
                    @else
                        <canvas id="goldLineChart" height="130"></canvas>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-coin me-1 text-secondary"></i>Monthly Silver Price</h6>
                </div>
                <div class="card-body">
                    @if($silverChartLabels->isEmpty())
                        <p class="text-center text-muted py-4">No silver price data available.</p>
                    @else
                        <canvas id="silverLineChart" height="130"></canvas>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Pie Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart me-1 text-warning"></i>Gold Sell vs Buy</h6>
                </div>
                <div class="card-body d-flex justify-content-center">
                    @if($stats['gold_sell_price'] === null && $stats['gold_buy_price'] === null)
                        <p class="text-center text-muted py-4">No gold price data available.</p>
                    @else
                        <canvas id="goldPieChart" style="max-height:220px;"></canvas>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart me-1 text-secondary"></i>Silver Sell vs Buy</h6>
                </div>
                <div class="card-body d-flex justify-content-center">
                    @if($stats['silver_sell_price'] === null && $stats['silver_buy_price'] === null)
                        <p class="text-center text-muted py-4">No silver price data available.</p>
                    @else
                        <canvas id="silverPieChart" style="max-height:220px;"></canvas>
                    @endif
                </div>
            </div>
        </div>

        {{-- Admin quick-links panel --}}
        <div class="col-md-6">
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
                        <a href="{{ route('admin.gold-price.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-graph-up-arrow me-1"></i>Gold Price
                        </a>
                        <a href="{{ route('admin.silver-price.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-coin me-1"></i>Silver Price
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================
         SECTION C — RECENT CHECK-INS
    ====================================================== --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-1"></i>Recent Check-ins</h6>
                    <a href="{{ route('admin.checkins.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
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

    {{-- ======================================================
         SECTION D — UPCOMING EVENTS  &  SECTION E — LATEST USERS
    ====================================================== --}}
    <div class="row g-3 mb-4">

        {{-- Upcoming Events --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar2-event me-1 text-success"></i>Upcoming Events</h6>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Event Name</th>
                                    <th>Start Date</th>
                                    <th>Location</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($upcoming_events as $event)
                                    <tr>
                                        <td>{{ $event->event_name }}</td>
                                        <td class="text-muted small">{{ $event->start_date->format('d M Y') }}</td>
                                        <td class="text-muted small">{{ $event->location ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $event->category_event === 'online' ? 'info' : 'primary' }}">
                                                {{ ucfirst($event->category_event ?? '-') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $event->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($event->status ?? '-') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No upcoming events.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Latest Users --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-person-plus me-1 text-primary"></i>Latest Users</h6>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Meem Code</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latest_users as $user)
                                    <tr>
                                        <td>{{ $user->fullname }}</td>
                                        <td><span class="badge bg-secondary">{{ $user->meem_code ?? '-' }}</span></td>
                                        <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No users yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ======================================================
         SECTION F — APK MANAGEMENT
    ====================================================== --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-android2 me-1 text-success"></i>APK Management</h6>
                    <a href="{{ route('admin.apk-detail.index') }}" class="btn btn-sm btn-outline-secondary">Manage APKs</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>APK Name</th>
                                    <th>Description</th>
                                    <th>Upload Date</th>
                                    <th>Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($apk_list as $apk)
                                    <tr>
                                        <td>{{ $apk->original_filename }}</td>
                                        <td class="text-muted small" style="white-space: pre-wrap;">{{ $apk->description ?? '-' }}</td>
                                        <td class="text-muted small">{{ $apk->uploaded_date ? $apk->uploaded_date->format('d M Y') : '-' }}</td>
                                        <td>
                                            @if($apk->download_link)
                                                <a href="{{ $apk->download_link }}" class="btn btn-sm btn-success" target="_blank">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                            @else
                                                <span class="text-muted small">No link</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No APK files uploaded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================
         PHASE 4 — CHARTS / JS  (Chart.js via CDN)
    ====================================================== --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
    $(function () {

        // ── Monthly Gold Line Chart ──────────────────────────────────────
        @if(!$goldChartLabels->isEmpty())
        (function () {
            var ctx = document.getElementById('goldLineChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($goldChartLabels),
                    datasets: [
                        {
                            label: 'Sell Price (MYR)',
                            data: @json($goldChartSell),
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245,158,11,0.1)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 3
                        },
                        {
                            label: 'Buy Price (MYR)',
                            data: @json($goldChartBuy),
                            borderColor: '#d97706',
                            backgroundColor: 'rgba(217,119,6,0.08)',
                            tension: 0.3,
                            fill: false,
                            pointRadius: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: { ticks: { callback: function (v) { return 'MYR ' + v.toLocaleString(); } } }
                    }
                }
            });
        }());
        @endif

        // ── Monthly Silver Line Chart ────────────────────────────────────
        @if(!$silverChartLabels->isEmpty())
        (function () {
            var ctx = document.getElementById('silverLineChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($silverChartLabels),
                    datasets: [
                        {
                            label: 'Sell Price (MYR)',
                            data: @json($silverChartSell),
                            borderColor: '#6b7280',
                            backgroundColor: 'rgba(107,114,128,0.1)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 3
                        },
                        {
                            label: 'Buy Price (MYR)',
                            data: @json($silverChartBuy),
                            borderColor: '#374151',
                            backgroundColor: 'rgba(55,65,81,0.08)',
                            tension: 0.3,
                            fill: false,
                            pointRadius: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: { ticks: { callback: function (v) { return 'MYR ' + v.toLocaleString(); } } }
                    }
                }
            });
        }());
        @endif

        // ── Gold Sell vs Buy Pie Chart ───────────────────────────────────
        @if($stats['gold_sell_price'] !== null || $stats['gold_buy_price'] !== null)
        (function () {
            var ctx = document.getElementById('goldPieChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sell Price', 'Buy Price'],
                    datasets: [{
                        data: @json([(float) ($stats['gold_sell_price'] ?? 0), (float) ($stats['gold_buy_price'] ?? 0)]),
                        backgroundColor: ['#f59e0b', '#d97706'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ctx.label + ': MYR ' + ctx.parsed.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    }
                }
            });
        }());
        @endif

        // ── Silver Sell vs Buy Pie Chart ─────────────────────────────────
        @if($stats['silver_sell_price'] !== null || $stats['silver_buy_price'] !== null)
        (function () {
            var ctx = document.getElementById('silverPieChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sell Price', 'Buy Price'],
                    datasets: [{
                        data: @json([(float) ($stats['silver_sell_price'] ?? 0), (float) ($stats['silver_buy_price'] ?? 0)]),
                        backgroundColor: ['#6b7280', '#374151'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ctx.label + ': MYR ' + ctx.parsed.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    }
                }
            });
        }());
        @endif

    });
    </script>
    @endpush

</x-app-layout>
