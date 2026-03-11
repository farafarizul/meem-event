@extends('layouts.public')

@section('title', 'Check-in Successful!')

@section('content')

{{-- Hero Section --}}
<section class="hero">
    <div class="hero-badges">
        <span class="chip">✅ Check-in Successful</span>
        <span class="chip">📅 {{ ucfirst($event->category_event) }}</span>
    </div>
    <h1>You're checked in!</h1>
    <p>Your attendance has been recorded for {{ $event->event_name }}.</p>
</section>

{{-- Details Card --}}
<div class="card">
    <div class="section-head">
        <h2>Attendance Confirmed</h2>
        <span class="count-pill">✓ Done</span>
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
            <small>Event</small>
            <strong>{{ $event->event_name }}</strong>
        </div>
        <div class="info-card">
            <small>Checked In At</small>
            <strong>{{ $checkin->checked_in_at->format('d M Y, H:i') }}</strong>
        </div>
    </div>

    <div class="action-row">
        <a href="{{ route('checkin.show', $event->unique_identifier) }}" class="btn btn-secondary">
            ← Back to Check-in
        </a>
    </div>
</div>

@endsection
