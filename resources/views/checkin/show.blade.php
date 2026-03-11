@extends('layouts.public')

@section('title', 'Check-in — ' . $event->event_name)

@section('content')

{{-- Hero Section --}}
<section class="hero">
    <div class="hero-badges">
        <span class="chip">📅 {{ ucfirst($event->category_event) }}</span>
        <span class="chip">📍 {{ $event->location }}</span>
    </div>
    <h1>{{ $event->event_name }}</h1>
    <p>
        @isset($user)
            Please confirm your details below to check in for this event.
        @else
            Select your name below to check in for this event.
        @endisset
    </p>
    <div class="metric-row">
        <div class="metric">
            <strong>{{ $event->start_date->format('d M Y') }}</strong>
            <span>Start Date</span>
        </div>
        <div class="metric">
            <strong>{{ $event->end_date->format('d M Y') }}</strong>
            <span>End Date</span>
        </div>
    </div>
</section>

@isset($user)
    {{-- ── QR flow: pre-identified user ── --}}
    <div class="card">
        <div class="section-head">
            <div>
                <h2>Confirm Check-in</h2>
                <p>Please verify that the details below are correct before checking in.</p>
            </div>
            <span class="count-pill">🔍 Confirm</span>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <small>Name</small>
                <strong>{{ $user->fullname }}</strong>
            </div>
            <div class="info-card">
                <small>Meem Code</small>
                <strong><span class="code-badge">{{ $user->meem_code }}</span></strong>
            </div>
            <div class="info-card">
                <small>Phone Number</small>
                <strong>{{ $user->phone_number }}</strong>
            </div>
            <div class="info-card">
                <small>Event</small>
                <strong>{{ $event->event_name }}</strong>
            </div>
        </div>

        <form action="{{ route('checkin.qr.store') }}" method="POST" id="checkin-form">
            @csrf
            <input type="hidden" name="scannedfromapp" value="{{ $scannedfromapp }}">
            <input type="hidden" name="user_id" value="{{ $obfuscatedUserId }}">

            <div class="action-row">
                <button type="button" class="btn btn-primary btn-submit" id="checkin-btn">
                    <i class="bi bi-check2-circle"></i> Check Me In
                </button>
            </div>
        </form>
    </div>
@else
    {{-- ── List flow: user selects from list ── --}}
    <div class="card">
        <div class="section-head">
            <h2>Check-in</h2>
            <span class="count-pill">Select Account</span>
        </div>

        <form action="{{ route('checkin.store', $event->unique_identifier) }}" method="POST" id="checkin-form">
            @csrf

            <div class="field">
                <label class="field-label" for="meem_code_search">Search by Meem Code or Name</label>
                <div class="control">
                    <span>🔍</span>
                    <input type="text" id="meem_code_search"
                        placeholder="Type your Meem Code or name…">
                </div>
            </div>

            <div class="field">
                <label class="field-label">Select your account</label>
                <div id="user-list">
                    @foreach ($users as $user)
                        <label class="user-option"
                            data-meem="{{ strtolower($user->meem_code) }}"
                            data-name="{{ strtolower($user->fullname) }}">
                            <input type="radio" name="user_id" value="{{ $user->user_id }}" required>
                            <div>
                                <div class="user-name">{{ $user->fullname }}</div>
                                <div class="user-meta">
                                    <span class="code-badge">{{ $user->meem_code }}</span>{{ $user->phone_number }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('user_id')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-submit">
                <i class="bi bi-check2-circle"></i> Check Me In
            </button>
        </form>
    </div>
@endisset

@endsection

@push('scripts')
@isset($user)
{{-- SweetAlert for QR flow confirmation --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
    $('#checkin-btn').on('click', function () {
        Swal.fire({
            title: 'Check-in Confirmation',
            text: 'Are you sure you want to check-in?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Check Me In!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#9e7b27',
            cancelButtonColor: '#6f7a8a',
        }).then(function (result) {
            if (result.isConfirmed) {
                $('#checkin-form').submit();
            }
        });
    });
</script>
@else
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
        $('.user-option').removeClass('selected');
        $(this).closest('.user-option').addClass('selected');
    });
</script>
@endisset
@endpush
