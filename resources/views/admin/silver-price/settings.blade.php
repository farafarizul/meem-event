<x-app-layout>
    <x-slot name="header">Silver Price Sync Settings</x-slot>

    <div class="nk-block">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title">
                                <h6 class="title"><em class="icon ni ni-setting me-1"></em>Silver Price Sync Settings</h6>
                            </div>
                        </div>
                        <div class="card-inner">
                            <p class="text-soft mb-4">
                                This setting controls how often the system is allowed to call the external silver price API.
                                The Linux cron remains running every minute — this setting controls the internal sync frequency.
                            </p>

                            <form method="POST" action="{{ route('admin.silver-price.settings.update') }}">
                                @csrf

                                <div class="form-group mb-4">
                                    <label for="interval_minutes" class="form-label fw-bold">Sync Interval</label>
                                    <div class="form-control-wrap">
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
                                </div>

                                <div class="alert alert-info d-flex align-items-start gap-2 mb-4" role="alert">
                                    <em class="icon ni ni-info-fill mt-1"></em>
                                    <div>
                                        <strong>Note:</strong> The actual Linux cron job runs every minute
                                        (<code>* * * * *</code>). This setting determines the minimum time between
                                        real API calls. The scheduler will skip the API call if the configured
                                        interval has not elapsed since the last sync.
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <em class="icon ni ni-save me-1"></em>Save Setting
                                    </button>
                                    <a href="{{ route('admin.silver-price.index') }}" class="btn btn-outline-secondary">
                                        <em class="icon ni ni-arrow-left me-1"></em>Back to Silver Price History
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
