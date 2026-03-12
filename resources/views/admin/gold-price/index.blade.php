<x-app-layout>
    <x-slot name="header">Gold Price History</x-slot>

    {{-- Latest Price Summary Cards --}}
    @if ($latest)
    <div class="nk-block nk-block-lg">
        <div class="row g-gs">
            <div class="col-md-3 col-sm-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-0">
                            <div class="card-title">
                                <h6 class="subtitle">Sell Price / gram</h6>
                            </div>
                            <div class="card-tools">
                                <em class="icon ni ni-sign-myr text-warning" style="font-size:1.5rem;"></em>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount text-success">MYR {{ number_format($latest->sell_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-0">
                            <div class="card-title">
                                <h6 class="subtitle">Buy Price / gram</h6>
                            </div>
                            <div class="card-tools">
                                <em class="icon ni ni-sign-myr text-info" style="font-size:1.5rem;"></em>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount text-info">MYR {{ number_format($latest->buy_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-0">
                            <div class="card-title">
                                <h6 class="subtitle">API Last Updated</h6>
                            </div>
                            <div class="card-tools">
                                <em class="icon ni ni-clock text-secondary" style="font-size:1.5rem;"></em>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount fs-6">{{ $latest->last_updated->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-0">
                            <div class="card-title">
                                <h6 class="subtitle">Synced At</h6>
                            </div>
                            <div class="card-tools">
                                <em class="icon ni ni-server text-primary" style="font-size:1.5rem;"></em>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount fs-6">{{ $latest->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title"><em class="icon ni ni-trend-up me-1"></em>All Gold Price Records</h6>
                        </div>
                        <div class="card-tools">
                            <form method="POST" action="{{ route('admin.gold-price.sync-now') }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <em class="icon ni ni-reload me-1"></em>Run Sync Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-inner p-0">
                    <div class="table-responsive">
                        <table id="gold-price-table" class="table table-orders w-100">
                            <thead class="tb-odr-head">
                                <tr class="tb-odr-item">
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
        </div>
    </div>

    @push('scripts')
    <script>
        $('#gold-price-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.gold-price.datatable') }}',
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex',   name: 'DT_RowIndex',   orderable: false, searchable: false, width: '40px' },
                { data: 'gold_price_id', name: 'gold_price_id', width: '60px' },
                { data: 'type',          name: 'type' },
                { data: 'product',       name: 'product' },
                { data: 'unit',          name: 'unit' },
                { data: 'currency',      name: 'currency' },
                { data: 'sell_price',    name: 'sell_price', className: 'text-end' },
                { data: 'buy_price',     name: 'buy_price',  className: 'text-end' },
                { data: 'timezone',      name: 'timezone' },
                { data: 'last_updated',  name: 'last_updated' },
                { data: 'created_at',    name: 'created_at' }
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
