<x-app-layout>
    <x-slot name="header">Create Event</x-slot>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-plus-circle me-1"></i>New Event</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.events.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Branch</label>
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

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Event Name <span class="text-danger">*</span></label>
                                <input type="text" name="event_name"
                                    class="form-control @error('event_name') is-invalid @enderror"
                                    value="{{ old('event_name') }}" required>
                                @error('event_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
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

                            <div class="col-12">
                                <label class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                                <input type="text" name="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    value="{{ old('location') }}" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date"
                                    class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Unique Identifier <span class="text-danger">*</span>
                                    <small class="text-muted fw-normal ms-1">Format: EVENT-XXXXXXXXXX (10 uppercase alphanumeric)</small>
                                </label>
                                <div class="input-group">
                                    <input type="text" name="unique_identifier" id="unique_identifier"
                                        class="form-control font-monospace text-uppercase @error('unique_identifier') is-invalid @enderror"
                                        value="{{ old('unique_identifier') }}"
                                        placeholder="EVENT-XXXXXXXXXX"
                                        maxlength="16" required>
                                    <button type="button" class="btn btn-outline-secondary" id="btn-generate-id">
                                        <i class="bi bi-shuffle me-1"></i>Generate
                                    </button>
                                    @error('unique_identifier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Create Event
                            </button>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Cancel
                            </a>
                        </div>
                    </form>
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
