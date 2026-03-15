<x-app-layout>
    <x-slot name="header">User Detail</x-slot>

    @push('styles')
    {{-- Select2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        .tab-pane-loading { min-height: 120px; }
        .nav-tabs .nav-link { font-weight: 500; }
        pre.json-viewer {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: .375rem;
            padding: 1rem;
            max-height: 420px;
            overflow: auto;
            font-size: .8rem;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .json-string  { color: #198754; }
        .json-number  { color: #0d6efd; }
        .json-boolean { color: #fd7e14; }
        .json-null    { color: #6c757d; }
        .json-key     { color: #842029; }
        .session-filter-select { min-width: 250px; }
    </style>
    @endpush

    {{-- Back button --}}
    <div class="mb-3">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Users
        </a>
    </div>

    {{-- User header card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div>
                    @if ($user->profile_picture)
                        @php
                            $picUrl = str_starts_with($user->profile_picture, 'http')
                                ? $user->profile_picture
                                : asset('storage/' . $user->profile_picture);
                        @endphp
                        <img src="{{ $picUrl }}" alt="Profile"
                             class="rounded-circle"
                             style="width:80px;height:80px;object-fit:cover;border:3px solid #dee2e6;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                             style="width:80px;height:80px;">
                            <i class="bi bi-person-fill text-white fs-2"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="mb-1 fw-bold">{{ $user->fullname }}</h4>
                    <div class="d-flex flex-wrap gap-3 text-muted small">
                        <span><i class="bi bi-qr-code me-1"></i>{{ $user->meem_code ?? '—' }}</span>
                        <span><i class="bi bi-hash me-1"></i>{{ $user->meem_id ?? '—' }}</span>
                        <span><i class="bi bi-envelope me-1"></i>{{ $user->email ?? '—' }}</span>
                        <span><i class="bi bi-telephone me-1"></i>{{ $user->phone_number }}</span>
                        <span><i class="bi bi-calendar me-1"></i>Registered {{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top cards row --}}
    <div class="row g-3 mb-4">

        {{-- Card 1: Introducer --}}
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <i class="bi bi-person-badge me-1"></i> Introducer
                </div>
                <div class="card-body">
                    @if ($introducer)
                        <dl class="row mb-0 small">
                            <dt class="col-5 text-muted">Name</dt>
                            <dd class="col-7 mb-2">{{ $introducer['name'] ?? '—' }}</dd>
                            <dt class="col-5 text-muted">CS Code</dt>
                            <dd class="col-7 mb-2">{{ $introducer['cs_code'] ?? '—' }}</dd>
                            <dt class="col-5 text-muted">Email</dt>
                            <dd class="col-7 mb-2" style="word-break:break-all;">{{ $introducer['email'] ?? '—' }}</dd>
                            <dt class="col-5 text-muted">Contact No</dt>
                            <dd class="col-7 mb-0">{{ $introducer['contact_no'] ?? '—' }}</dd>
                        </dl>
                    @else
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>No introducer data available.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 2: GSS (Gold Safe Storage) --}}
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-warning py-2">
                    <i class="bi bi-safe me-1"></i> GSS (Gold Safe Storage)
                </div>
                <div class="card-body">
                    @if ($gssDetail)
                        @php
                            $balance     = $gssDetail['balance'] ?? '—';
                            $goldPrice   = $gssDetail['gold_price'] ?? '—';
                            $goldValue   = $gssDetail['gold_value'] ?? '—';
                            $gssProgress = $gssDetail['gss_progress'] ?? [];
                            $progressPct = isset($gssProgress['progress_percentage'])
                                ? min(100, (float) $gssProgress['progress_percentage'])
                                : null;
                            $threshold   = $gssProgress['threshold'] ?? null;
                        @endphp
                        <dl class="row mb-2 small">
                            <dt class="col-6 text-muted">Balance (g)</dt>
                            <dd class="col-6 mb-2">{{ $balance }}</dd>
                            <dt class="col-6 text-muted">Gold Price</dt>
                            <dd class="col-6 mb-2">RM {{ $goldPrice }}</dd>
                            <dt class="col-6 text-muted">Gold Value</dt>
                            <dd class="col-6 mb-0">RM {{ $goldValue }}</dd>
                        </dl>
                        @if ($progressPct !== null)
                            <div class="small text-muted mb-1">
                                Progress to {{ $threshold ?? '—' }} g
                                <span class="fw-semibold text-dark ms-1">{{ $progressPct }}%</span>
                            </div>
                            <div class="progress" style="height:10px;">
                                <div class="progress-bar bg-warning"
                                     role="progressbar"
                                     style="width:{{ $progressPct }}%;"
                                     aria-valuenow="{{ $progressPct }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        @endif
                    @else
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>No GSS data available.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 3: Session --}}
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <i class="bi bi-phone me-1"></i> App Sessions
                </div>
                <div class="card-body">
                    @if ($sessionStats && $sessionStats->total_logs > 0)
                        <dl class="row mb-0 small">
                            <dt class="col-7 text-muted">Total Logs</dt>
                            <dd class="col-5 mb-2 fw-semibold">{{ number_format($sessionStats->total_logs) }}</dd>
                            <dt class="col-7 text-muted">Distinct Sessions</dt>
                            <dd class="col-5 mb-2 fw-semibold">{{ number_format($sessionStats->distinct_sessions) }}</dd>
                            @if ($latestSession)
                                <dt class="col-7 text-muted">Latest Session</dt>
                                <dd class="col-5 mb-0">
                                    <span class="d-inline-block text-truncate"
                                          style="max-width:110px;vertical-align:bottom;"
                                          title="{{ $latestSession }}">
                                        {{ $latestSession }}
                                    </span>
                                </dd>
                            @endif
                        </dl>
                    @else
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>No session data available.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs section --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="userDetailTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active"
                            id="tab-btn-basic-info"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-pane-basic-info"
                            data-tab-name="basic-info"
                            type="button" role="tab">
                        <i class="bi bi-person-lines-fill me-1"></i>Maklumat Asas Pengguna
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                            id="tab-btn-events"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-pane-events"
                            data-tab-name="events"
                            type="button" role="tab">
                        <i class="bi bi-calendar2-event me-1"></i>Events
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                            id="tab-btn-logs"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-pane-logs"
                            data-tab-name="logs"
                            type="button" role="tab">
                        <i class="bi bi-journal-text me-1"></i>Logs
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="userDetailTabsContent">
                <div class="tab-pane fade show active tab-pane-loading"
                     id="tab-pane-basic-info" role="tabpanel">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted small">Loading...</p>
                    </div>
                </div>
                <div class="tab-pane fade tab-pane-loading"
                     id="tab-pane-events" role="tabpanel">
                </div>
                <div class="tab-pane fade tab-pane-loading"
                     id="tab-pane-logs" role="tabpanel">
                </div>
            </div>
        </div>
    </div>

    {{-- JSON View Modal (shared, reused by logs tab) --}}
    <div class="modal fade" id="jsonModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-braces me-1"></i>JSON Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="json-display" class="json-viewer"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        var tabLoaded = {};
        var tabUrls = {
            'basic-info': '{{ route('admin.users.tab.basic-info', $user) }}',
            'events':     '{{ route('admin.users.tab.events', $user) }}',
            'logs':       '{{ route('admin.users.tab.logs', $user) }}'
        };

        // JSON syntax highlighter (reused from logs page)
        function syntaxHighlight(json) {
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(
                /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
                function (match) {
                    var cls = 'json-number';
                    if (/^"/.test(match)) {
                        cls = /:$/.test(match) ? 'json-key' : 'json-string';
                    } else if (/true|false/.test(match)) {
                        cls = 'json-boolean';
                    } else if (/null/.test(match)) {
                        cls = 'json-null';
                    }
                    return '<span class="' + cls + '">' + match + '</span>';
                }
            );
        }

        function loadTab(tabName, extraParams) {
            var $pane = $('#tab-pane-' + tabName);
            if (!$pane.length) return;

            var url = tabUrls[tabName] || '';
            if (extraParams) {
                url += '?' + $.param(extraParams);
            }

            $pane.html(
                '<div class="text-center py-5">' +
                '<div class="spinner-border text-primary" role="status"></div>' +
                '<p class="mt-2 text-muted small">Loading...</p>' +
                '</div>'
            );

            $.get(url, function (html) {
                $pane.html(html);
                tabLoaded[tabName] = true;
                if (tabName === 'logs') {
                    initLogsSelect2();
                }
            }).fail(function () {
                $pane.html(
                    '<div class="alert alert-danger m-3">' +
                    '<i class="bi bi-exclamation-triangle me-1"></i>Failed to load content. ' +
                    '<button class="btn btn-sm btn-outline-danger ms-2" onclick="loadTab(\'' + tabName + '\')">Retry</button>' +
                    '</div>'
                );
            });
        }

        function initLogsSelect2() {
            var $filter = $('#session-filter');
            if (!$filter.length) return;

            if ($filter.data('select2')) {
                $filter.select2('destroy');
            }

            $filter.select2({
                theme: 'bootstrap-5',
                width: 'element',
                allowClear: true,
                placeholder: 'All Sessions'
            });

            $filter.off('change.logs').on('change.logs', function () {
                var session = $(this).val() || '';
                loadTab('logs', session ? { app_session: session } : null);
            });
        }

        // Tab click handler — load on demand
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var tabName = $(e.target).data('tab-name');
            if (tabName && !tabLoaded[tabName]) {
                loadTab(tabName);
            }
        });

        // Auto-load first tab on page entry
        $(function () {
            loadTab('basic-info');
        });

        // View JSON modal (delegated, works with dynamically loaded content)
        $(document).on('click', '.btn-view-json', function () {
            var raw = $(this).data('json') || '';
            var display;
            try {
                var parsed = JSON.parse(raw);
                display = syntaxHighlight(JSON.stringify(parsed, null, 2));
            } catch (e) {
                display = $('<div>').text(raw).html();
            }
            $('#json-display').html(display);
            $('#jsonModal').modal('show');
        });
    </script>
    @endpush
</x-app-layout>
