@extends('layouts.public')

@section('title', 'Already Checked In')

@section('content')

{{-- Hero Section --}}
<section class="hero">
    <div class="hero-badges">
        <span class="chip">⚠️ Already Checked In</span>
        <span class="chip">📅 {{ ucfirst($event->category_event) }}</span>
    </div>
    <h1>Already Checked In</h1>
    <p>You have already checked in for {{ $event->event_name }}.</p>
</section>

{{-- Details Card --}}
<div class="card">
    <div class="section-head">
        <h2>Check-in Record</h2>
        <span class="count-pill">⚠️ Recorded</span>
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
        <div class="info-card info-card-full">
            <small>Event</small>
            <strong>{{ $event->event_name }}</strong>
        </div>
    </div>

    <div class="action-row">
        <a href="{{ route('checkin.show', $event->unique_identifier) }}" class="btn btn-secondary">
            ← Back to Check-in
        </a>
    </div>
</div>

@endsection
