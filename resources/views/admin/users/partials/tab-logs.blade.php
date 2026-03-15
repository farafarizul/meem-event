{{-- Tab 3: Logs --}}

{{-- Stats cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-primary text-white text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold">{{ number_format($totalLogs) }}</div>
                <div class="small">Jumlah Log</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-info text-white text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold">{{ number_format($totalDistinctSessions) }}</div>
                <div class="small">Sesi Berbeza</div>
            </div>
        </div>
    </div>
</div>

{{-- Session filter --}}
<div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
    <label class="form-label fw-semibold mb-0">
        <i class="bi bi-funnel me-1"></i>Tapis Sesi:
    </label>
    <select id="session-filter" class="form-select form-select-sm session-filter-select">
        <option value="">Semua Sesi</option>
        @foreach ($distinctSessions as $session)
            <option value="{{ $session }}"
                {{ $selectedSession === $session ? 'selected' : '' }}>
                {{ $session }}
            </option>
        @endforeach
    </select>
</div>

{{-- Logs table --}}
<div class="table-responsive">
    @if ($logs->isNotEmpty())
        <table class="table table-hover align-middle table-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Tarikh / Masa</th>
                    <th>Modul</th>
                    <th>Kaedah</th>
                    <th>Operasi</th>
                    <th>Sesi</th>
                    <th>JSON Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $i => $log)
                    @php
                        $raw     = $log->log_data_json ?? '';
                        $preview = mb_strlen($raw) > 80 ? mb_substr($raw, 0, 80) . '…' : $raw;
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="text-nowrap small">{{ $log->create_dttm ?? '—' }}</td>
                        <td>{{ $log->trail_module ?? '—' }}</td>
                        <td>{{ $log->trail_method ?? '—' }}</td>
                        <td>{{ $log->trail_operation ?? '—' }}</td>
                        <td>
                            @if ($log->app_session)
                                <span class="d-inline-block text-truncate small"
                                      style="max-width:120px;"
                                      title="{{ $log->app_session }}">
                                    {{ $log->app_session }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($raw)
                                <span class="text-truncate d-inline-block small"
                                      style="max-width:140px;">{{ $preview }}</span>
                                <button type="button"
                                        class="btn btn-outline-secondary btn-view-json py-0 px-1 ms-1"
                                        style="font-size:0.7rem;"
                                        data-json="{{ e($raw) }}">
                                    <i class="bi bi-braces"></i> View
                                </button>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($logs->count() >= 50)
            <p class="text-muted small mt-2">
                <i class="bi bi-info-circle me-1"></i>Menunjukkan 50 rekod terkini.
                @if ($selectedSession)
                    Log ditapis mengikut sesi: <strong>{{ $selectedSession }}</strong>
                @endif
            </p>
        @endif
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
            @if ($selectedSession)
                Tiada log untuk sesi <strong>{{ $selectedSession }}</strong>.
            @else
                Tiada rekod log untuk pengguna ini.
            @endif
        </div>
    @endif
</div>
