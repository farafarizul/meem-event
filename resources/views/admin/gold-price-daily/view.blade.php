<x-app-layout>
    <x-slot name="header">Gold Price Daily — Detail</x-slot>

    <div class="mb-3">
        <a href="{{ route('admin.gold-price-daily.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

    {{-- Summary Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-calendar3 me-1"></i>
                {{ $goldPriceDaily->gold_price_date->format('d M Y') }}
            </h6>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.gold-price-daily.sync-this', $goldPriceDaily->gold_price_daily_id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning">
                        <i class="bi bi-arrow-repeat me-1"></i>Re-sync This Date
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.gold-price-daily.regen-ai', $goldPriceDaily->gold_price_daily_id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">
                        <i class="bi bi-robot me-1"></i>Regenerate AI Reason
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <th class="text-muted" style="width:45%">Date</th>
                                <td>{{ $goldPriceDaily->gold_price_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Avg Sell Price</th>
                                <td>{{ $goldPriceDaily->sell_price !== null ? 'MYR ' . number_format($goldPriceDaily->sell_price, 2) : '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Avg Buy Price</th>
                                <td>{{ $goldPriceDaily->buy_price !== null ? 'MYR ' . number_format($goldPriceDaily->buy_price, 2) : '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Open Price</th>
                                <td>{{ $goldPriceDaily->open_price !== null ? 'MYR ' . number_format($goldPriceDaily->open_price, 2) : '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <th class="text-muted" style="width:45%">Close Price</th>
                                <td>{{ $goldPriceDaily->close_price !== null ? 'MYR ' . number_format($goldPriceDaily->close_price, 2) : '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Highest Price</th>
                                <td>{{ $goldPriceDaily->highest_price !== null ? 'MYR ' . number_format($goldPriceDaily->highest_price, 2) : '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Lowest Price</th>
                                <td>{{ $goldPriceDaily->lowest_price !== null ? 'MYR ' . number_format($goldPriceDaily->lowest_price, 2) : '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Created At</th>
                                <td>{{ $goldPriceDaily->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Updated At</th>
                                <td>{{ $goldPriceDaily->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- AI Reason Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-robot me-1"></i>AI Reason (Bahasa Melayu)</h6>
        </div>
        <div class="card-body">
            @if ($goldPriceDaily->reason_from_ai)
                <p class="mb-0" style="white-space: pre-wrap;">{{ $goldPriceDaily->reason_from_ai }}</p>
            @else
                <p class="text-muted mb-0"><em>No AI reason available for this date.</em></p>
            @endif
        </div>
    </div>

    {{-- Intraday Records --}}
    @if ($intraday->isNotEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-clock-history me-1"></i>
                Intraday Records ({{ $intraday->count() }} rows)
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Sell Price</th>
                            <th>Buy Price</th>
                            <th>Timezone</th>
                            <th>Last Updated</th>
                            <th>Synced At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($intraday as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="text-end">MYR {{ number_format($row->sell_price, 2) }}</td>
                            <td class="text-end">MYR {{ number_format($row->buy_price, 2) }}</td>
                            <td>{{ $row->timezone ?? '—' }}</td>
                            <td>{{ $row->last_updated->format('d M Y H:i:s') }}</td>
                            <td>{{ $row->created_at->format('d M Y H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
