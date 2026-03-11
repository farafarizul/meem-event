@extends('layouts.public')

@section('title', 'Check-in — ' . $event->event_name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">

        {{-- Event Info Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-calendar2-event me-2"></i>{{ $event->event_name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <p class="mb-1 text-muted small fw-semibold text-uppercase">Category</p>
                        <span class="badge bg-{{ $event->category_event === 'online' ? 'info' : 'success' }} fs-6">
                            {{ ucfirst($event->category_event) }}
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <p class="mb-1 text-muted small fw-semibold text-uppercase">Location</p>
                        <p class="mb-0 fw-semibold">{{ $event->location }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="mb-1 text-muted small fw-semibold text-uppercase">Start Date</p>
                        <p class="mb-0">{{ $event->start_date->format('d M Y') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="mb-1 text-muted small fw-semibold text-uppercase">End Date</p>
                        <p class="mb-0">{{ $event->end_date->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Check-in Form --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-check2-circle me-2"></i>Check-in
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted">Select your name to check in for this event.</p>

                <form action="{{ route('checkin.store', $event->unique_identifier) }}" method="POST" id="checkin-form">
                    @csrf

                    <div class="mb-3">
                        <label for="meem_code_search" class="form-label fw-semibold">
                            Search by Meem Code or Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="meem_code_search"
                            placeholder="Type your Meem Code or name to search...">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Select your account</label>
                        <div id="user-list" style="max-height: 320px; overflow-y: auto;">
                            @foreach ($users as $user)
                                <label class="d-flex align-items-center gap-3 p-3 border rounded mb-2 user-option"
                                    style="cursor: pointer;"
                                    data-meem="{{ strtolower($user->meem_code) }}"
                                    data-name="{{ strtolower($user->fullname) }}">
                                    <input type="radio" name="user_id" value="{{ $user->id }}"
                                        class="form-check-input mt-0 flex-shrink-0" required>
                                    <div>
                                        <div class="fw-semibold">{{ $user->fullname }}</div>
                                        <div class="text-muted small">
                                            <span class="badge bg-secondary me-1">{{ $user->meem_code }}</span>
                                            {{ $user->phone_number }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('user_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check2-circle me-2"></i>Check Me In
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live search/filter user list
    $('#meem_code_search').on('input', function () {
        var val = $(this).val().toLowerCase().trim();
        $('.user-option').each(function () {
            var meem = $(this).data('meem');
            var name = $(this).data('name');
            if (!val || meem.indexOf(val) !== -1 || name.indexOf(val) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Highlight selected radio label
    $(document).on('change', 'input[name="user_id"]', function () {
        $('.user-option').removeClass('border-primary bg-primary bg-opacity-10');
        $(this).closest('.user-option').addClass('border-primary bg-primary bg-opacity-10');
    });
</script>
@endpush
