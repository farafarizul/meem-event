<x-app-layout>
    <x-slot name="header">Gold Price Daily</x-slot>

    {{-- Section A: Statistic Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-calendar3 fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_records']) }}</div>
                        <div class="text-muted small">Total Records</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="bi bi-calendar-check fs-4 text-success"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $stats['last_sync_date'] }}</div>
                        <div class="text-muted small">Last Sync Date</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                        <i class="bi bi-cash-coin fs-4 text-info"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold text-info">MYR {{ $stats['latest_avg_buy'] }}</div>
                        <div class="text-muted small">Latest Avg Buy Price</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-currency-dollar fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold text-warning">MYR {{ $stats['latest_avg_sell'] }}</div>
                        <div class="text-muted small">Latest Avg Sell Price</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="bi bi-graph-up fs-4 text-danger"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold text-danger">MYR {{ $stats['highest_buy'] }}</div>
                        <div class="text-muted small">Highest Recorded Buy</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                        <i class="bi bi-graph-down fs-4 text-secondary"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold text-secondary">MYR {{ $stats['lowest_buy'] }}</div>
                        <div class="text-muted small">Lowest Recorded Buy</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="bi bi-robot fs-4 text-success"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_with_ai']) }}</div>
                        <div class="text-muted small">Rows With AI Reason</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-clock-history fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $stats['last_updated_at'] }}</div>
                        <div class="text-muted small">Last Updated At</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section B: Manual Sync Panel --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-arrow-repeat me-1"></i>Manual Sync</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.gold-price-daily.manual-sync') }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-auto">
                    <label class="form-label fw-semibold mb-1">Select Date</label>
                    <input type="date" name="sync_date" class="form-control @error('sync_date') is-invalid @enderror"
                           value="{{ old('sync_date', date('Y-m-d')) }}" required>
                    @error('sync_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-arrow-repeat me-1"></i>Sync Selected Date
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-secondary" id="btn-sync-yesterday">
                        <i class="bi bi-calendar-minus me-1"></i>Sync Yesterday
                    </button>
                </div>
            </form>
            <p class="text-muted small mt-2 mb-0">
                This will aggregate daily summary from <code>gold_price</code> records and call Groq AI for the selected date.
            </p>
        </div>
    </div>

    {{-- Section C: DataTable --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-table me-1"></i>Daily Gold Price Records</h6>
        </div>
        <div class="card-body">

            {{-- Filters --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="form-label small mb-1">Date From</label>
                    <input type="date" id="filter-date-from" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-1">Date To</label>
                    <input type="date" id="filter-date-to" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-1">AI Reason</label>
                    <select id="filter-has-reason" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="1">Has AI Reason</option>
                        <option value="0">No AI Reason</option>
                    </select>
                </div>
                <div class="col-auto d-flex align-items-end">
                    <button id="btn-apply-filters" class="btn btn-sm btn-primary me-1">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <button id="btn-clear-filters" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="gold-price-daily-table" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Avg Sell</th>
                            <th>Avg Buy</th>
                            <th>Open</th>
                            <th>Close</th>
                            <th>Highest</th>
                            <th>Lowest</th>
                            <th>AI Reason</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        var table = $('#gold-price-daily-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.gold-price-daily.datatable') }}',
                type: 'GET',
                data: function (d) {
                    d.date_from  = $('#filter-date-from').val();
                    d.date_to    = $('#filter-date-to').val();
                    d.has_reason = $('#filter-has-reason').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex',         name: 'DT_RowIndex',   orderable: false, searchable: false, width: '40px' },
                { data: 'gold_price_date',      name: 'gold_price_date' },
                { data: 'sell_price',           name: 'sell_price',    className: 'text-end' },
                { data: 'buy_price',            name: 'buy_price',     className: 'text-end' },
                { data: 'open_price',           name: 'open_price',    className: 'text-end' },
                { data: 'close_price',          name: 'close_price',   className: 'text-end' },
                { data: 'highest_price',        name: 'highest_price', className: 'text-end' },
                { data: 'lowest_price',         name: 'lowest_price',  className: 'text-end' },
                { data: 'reason_from_ai',       name: 'reason_from_ai', orderable: false },
                { data: 'created_at',           name: 'created_at' },
                { data: 'updated_at',           name: 'updated_at' },
                { data: 'action',               name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[1, 'desc']],
            pageLength: 25,
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...'
            }
        });

        $('#btn-apply-filters').on('click', function () {
            table.ajax.reload();
        });

        $('#btn-clear-filters').on('click', function () {
            $('#filter-date-from').val('');
            $('#filter-date-to').val('');
            $('#filter-has-reason').val('');
            table.ajax.reload();
        });

        $('#btn-sync-yesterday').on('click', function () {
            var yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            var y = yesterday.getFullYear();
            var m = String(yesterday.getMonth() + 1).padStart(2, '0');
            var d = String(yesterday.getDate()).padStart(2, '0');
            $('[name="sync_date"]').val(y + '-' + m + '-' + d);
            $('[name="sync_date"]').closest('form').submit();
        });
    </script>
    @endpush
</x-app-layout>
