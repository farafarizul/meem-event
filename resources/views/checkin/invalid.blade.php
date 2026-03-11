@extends('layouts.public')

@section('title', 'Invalid Check-in Link')

@section('content')

    <section class="hero">
        <div class="hero-badges">
            <span class="chip">❌ Invalid Link</span>
        </div>
        <h1>Invalid Check-in Link</h1>
        <p>This check-in link is invalid or has expired. Please scan the QR code again.</p>
    </section>

    <div class="card">
        <div class="section-head">
            <div>
                <h2>Unable to Process</h2>
                <p>We could not process your check-in request.</p>
            </div>
            <span class="count-pill">❌ Error</span>
        </div>

        <p style="color: var(--muted); font-size: 14px; line-height: 1.6; margin: 0 0 12px;">
            This can happen if:
        </p>
        <ul style="color: var(--muted); font-size: 14px; line-height: 2; margin: 0; padding-left: 20px;">
            <li>The QR code is damaged or incomplete</li>
            <li>The event no longer exists</li>
            <li>The user account was not found</li>
            <li>The check-in link has been modified</li>
        </ul>

        <p style="color: var(--muted); font-size: 14px; line-height: 1.6; margin: 16px 0 8px;">
            URL opened:
        </p>
        <div style="background:#f8f9fb; border:1px solid #e5e7eb; border-radius:8px; padding:12px; font-size:13px; word-break:break-all;">
            {{ request()->fullUrl() }}
        </div>
    </div>

@endsection
