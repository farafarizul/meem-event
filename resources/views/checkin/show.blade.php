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
    <p>Select your name below to check in for this event.</p>
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

{{-- Check-in Form Card --}}
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
                        <input type="radio" name="user_id" value="{{ $user->id }}" required>
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
        $('.user-option').removeClass('selected');
        $(this).closest('.user-option').addClass('selected');
    });
</script>
@endpush
