<x-app-layout>
    <x-slot name="header">Edit Event</x-slot>

    <div class="nk-block">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title"><em class="icon ni ni-edit me-1"></em>Edit Event</h6>
                                </div>
                                <div class="card-tools">
                                    <span class="badge bg-secondary font-monospace">{{ $event->unique_identifier }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner">
                            <form action="{{ route('admin.events.update', $event) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Branch</label>
                                            <div class="form-control-wrap">
                                                <select name="branch_id"
                                                    class="form-select @error('branch_id') is-invalid @enderror">
                                                    <option value="">-- Select Branch --</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->branch_id }}" {{ old('branch_id', $event->branch_id) == $branch->branch_id ? 'selected' : '' }}>
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
                                                    value="{{ old('event_name', $event->event_name) }}" required>
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
                                                    <option value="online" {{ old('category_event', $event->category_event) === 'online' ? 'selected' : '' }}>Online</option>
                                                    <option value="onsite" {{ old('category_event', $event->category_event) === 'onsite' ? 'selected' : '' }}>Onsite</option>
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
                                                    value="{{ old('location', $event->location) }}" required>
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
                                                    value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required>
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
                                                    value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" required>
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
                                                <input type="text" name="unique_identifier"
                                                    class="form-control font-monospace text-uppercase @error('unique_identifier') is-invalid @enderror"
                                                    value="{{ old('unique_identifier', $event->unique_identifier) }}"
                                                    maxlength="16" required>
                                                @error('unique_identifier')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <em class="icon ni ni-save me-1"></em>Update Event
                                    </button>
                                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                                        <em class="icon ni ni-arrow-left me-1"></em>Back to List
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
