<x-app-layout>
    <x-slot name="header">Gold Price Sync Settings</x-slot>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-gear me-1"></i>Gold Price Sync Settings</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        This setting controls how often the system is allowed to call the external gold price API.
                        The Linux cron remains running every minute — this setting controls the internal sync frequency.
                    </p>

                    <form method="POST" action="{{ route('admin.gold-price.settings.update') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="interval_minutes" class="form-label fw-semibold">Sync Interval</label>
                            <select name="interval_minutes" id="interval_minutes"
                                    class="form-select @error('interval_minutes') is-invalid @enderror">
                                @foreach ([5, 10, 15, 30] as $option)
                                    <option value="{{ $option }}" @selected($currentInterval == $option)>
                                        Every {{ $option }} minutes
                                    </option>
                                @endforeach
                            </select>
                            @error('interval_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info d-flex align-items-start gap-2 mb-4" role="alert">
                            <i class="bi bi-info-circle-fill mt-1"></i>
                            <div>
                                <strong>Note:</strong> The actual Linux cron job runs every minute
                                (<code>* * * * *</code>). This setting determines the minimum time between
                                real API calls. The scheduler will skip the API call if the configured
                                interval has not elapsed since the last sync.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Save Setting
                        </button>
                        <a href="{{ route('admin.gold-price.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-left me-1"></i>Back to Gold Price History
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
