<x-app-layout>
    <x-slot name="header">Create Event</x-slot>

    <div class="nk-block">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title">
                                <h6 class="title"><em class="icon ni ni-plus-circle me-1"></em>New Event</h6>
                            </div>
                        </div>
                        <div class="card-inner">
                            <form action="{{ route('admin.events.store') }}" method="POST">
                                @csrf

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Branch</label>
                                            <div class="form-control-wrap">
                                                <select name="branch_id"
                                                    class="form-select @error('branch_id') is-invalid @enderror">
                                                    <option value="">-- Select Branch --</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->branch_id }}" {{ old('branch_id') == $branch->branch_id ? 'selected' : '' }}>
                                                            {{ $branch->branch_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('branch_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Event Name <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="event_name"
                                                    class="form-control @error('event_name') is-invalid @enderror"
                                                    value="{{ old('event_name') }}" required>
                                                @error('event_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <select name="category_event"
                                                    class="form-select @error('category_event') is-invalid @enderror" required>
                                                    <option value="">-- Select Category --</option>
                                                    <option value="online" {{ old('category_event') === 'online' ? 'selected' : '' }}>Online</option>
                                                    <option value="onsite" {{ old('category_event') === 'onsite' ? 'selected' : '' }}>Onsite</option>
                                                </select>
                                                @error('category_event')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Location <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="location"
                                                    class="form-control @error('location') is-invalid @enderror"
                                                    value="{{ old('location') }}" required>
                                                @error('location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="date" name="start_date"
                                                    class="form-control @error('start_date') is-invalid @enderror"
                                                    value="{{ old('start_date') }}" required>
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="date" name="end_date"
                                                    class="form-control @error('end_date') is-invalid @enderror"
                                                    value="{{ old('end_date') }}" required>
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">
                                                Unique Identifier <span class="text-danger">*</span>
                                                <small class="text-soft fw-normal ms-1">Format: EVENT-XXXXXXXXXX (10 uppercase alphanumeric)</small>
                                            </label>
                                            <div class="form-control-wrap">
                                                <div class="input-group">
                                                    <input type="text" name="unique_identifier" id="unique_identifier"
                                                        class="form-control font-monospace text-uppercase @error('unique_identifier') is-invalid @enderror"
                                                        value="{{ old('unique_identifier') }}"
                                                        placeholder="EVENT-XXXXXXXXXX"
                                                        maxlength="16" required>
                                                    <button type="button" class="btn btn-outline-secondary" id="btn-generate-id">
                                                        <em class="icon ni ni-shuffle me-1"></em>Generate
                                                    </button>
                                                    @error('unique_identifier')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <em class="icon ni ni-save me-1"></em>Create Event
                                    </button>
                                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                                        <em class="icon ni ni-arrow-left me-1"></em>Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $('#btn-generate-id').on('click', function () {
            var chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var result = 'EVENT-';
            for (var i = 0; i < 10; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            $('#unique_identifier').val(result);
        });
    </script>
    @endpush
</x-app-layout>
