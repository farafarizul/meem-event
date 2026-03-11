<x-app-layout>
    <x-slot name="header">Edit Event</x-slot>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-pencil me-1"></i>Edit Event</h6>
                    <span class="badge bg-secondary font-monospace">{{ $event->unique_identifier }}</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.events.update', $event) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Event Name <span class="text-danger">*</span></label>
                                <input type="text" name="event_name"
                                    class="form-control @error('event_name') is-invalid @enderror"
                                    value="{{ old('event_name', $event->event_name) }}" required>
                                @error('event_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category_event"
                                    class="form-select @error('category_event') is-invalid @enderror" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="online" {{ old('category_event', $event->category_event) === 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="onsite" {{ old('category_event', $event->category_event) === 'onsite' ? 'selected' : '' }}>Onsite</option>
                                </select>
                                @error('category_event')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                                <input type="text" name="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    value="{{ old('location', $event->location) }}" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date"
                                    class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Unique Identifier <span class="text-danger">*</span>
                                    <small class="text-muted fw-normal ms-1">Format: EVENT-XXXXXXXXXX (10 uppercase alphanumeric)</small>
                                </label>
                                <input type="text" name="unique_identifier"
                                    class="form-control font-monospace text-uppercase @error('unique_identifier') is-invalid @enderror"
                                    value="{{ old('unique_identifier', $event->unique_identifier) }}"
                                    maxlength="16" required>
                                @error('unique_identifier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Update Event
                            </button>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
