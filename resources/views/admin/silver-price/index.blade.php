<x-app-layout>
    <x-slot name="header">Silver Price History</x-slot>

    {{-- Latest Price Summary Card --}}
    @if ($latest)
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                        <i class="bi bi-currency-dollar fs-4 text-secondary"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-success">MYR {{ number_format($latest->sell_price, 2) }}</div>
                        <div class="text-muted small">Sell Price / gram</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                        <i class="bi bi-cash-coin fs-4 text-info"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-info">MYR {{ number_format($latest->buy_price, 2) }}</div>
                        <div class="text-muted small">Buy Price / gram</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                        <i class="bi bi-clock-history fs-4 text-secondary"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $latest->last_updated->format('d M Y H:i') }}</div>
                        <div class="text-muted small">API Last Updated</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-database-check fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $latest->created_at->format('d M Y H:i') }}</div>
                        <div class="text-muted small">Synced At</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-graph-up-arrow me-1"></i>All Silver Price Records</h6>
            <form method="POST" action="{{ route('admin.silver-price.sync-now') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="bi bi-arrow-repeat me-1"></i>Run Sync Now
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="silver-price-table" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Product</th>
                            <th>Unit</th>
                            <th>Currency</th>
                            <th>Sell Price</th>
                            <th>Buy Price</th>
                            <th>Timezone</th>
                            <th>API Last Updated</th>
                            <th>Synced At</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $('#silver-price-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.silver-price.datatable') }}',
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex',      name: 'DT_RowIndex',      orderable: false, searchable: false, width: '40px' },
                { data: 'silver_price_id',  name: 'silver_price_id',  width: '60px' },
                { data: 'type',             name: 'type' },
                { data: 'product',          name: 'product' },
                { data: 'unit',             name: 'unit' },
                { data: 'currency',         name: 'currency' },
                { data: 'sell_price',       name: 'sell_price', className: 'text-end' },
                { data: 'buy_price',        name: 'buy_price',  className: 'text-end' },
                { data: 'timezone',         name: 'timezone' },
                { data: 'last_updated',     name: 'last_updated' },
                { data: 'created_at',       name: 'created_at' }
            ],
            order: [[1, 'desc']],
            pageLength: 25,
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...'
            }
        });
    </script>
    @endpush
</x-app-layout>
