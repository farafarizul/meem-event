@extends('layouts.public')

@section('title', 'Check-in Successful!')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5 text-center">

        <div class="card border-0 shadow-sm">
            <div class="card-body py-5 px-4">
                <div class="mb-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex p-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    </div>
                </div>

                <h4 class="fw-bold text-success mb-1">Check-in Successful!</h4>
                <p class="text-muted mb-4">Your attendance has been recorded.</p>

                <div class="bg-light rounded p-3 mb-4 text-start">
                    <div class="row g-2">
                        <div class="col-5 text-muted small fw-semibold">Name</div>
                        <div class="col-7 fw-semibold small">{{ $user->fullname }}</div>

                        <div class="col-5 text-muted small fw-semibold">Meem Code</div>
                        <div class="col-7">
                            <span class="badge bg-secondary">{{ $user->meem_code }}</span>
                        </div>

                        <div class="col-5 text-muted small fw-semibold">Event</div>
                        <div class="col-7 small">{{ $event->event_name }}</div>

                        <div class="col-5 text-muted small fw-semibold">Checked In At</div>
                        <div class="col-7 small">{{ $checkin->checked_in_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                <a href="{{ route('checkin.show', $event->unique_identifier) }}"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Check-in Page
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
