@extends('layouts.public')

@section('title', 'User Profile | MEEM Gold')

@push('styles')
<style>
/* Profile-page extras */
.hero { text-align: center; }
.avatar-wrap {
    width: 112px; height: 112px; margin: 0 auto 14px;
    padding: 4px; border-radius: 999px;
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(240,209,125,0.9));
    box-shadow: 0 14px 34px rgba(6,19,34,0.28);
}
.avatar {
    width: 100%; height: 100%; object-fit: cover;
    border-radius: 999px; background: #dfe6ef;
    border: 3px solid rgba(255,255,255,0.78);
}
.action-row { justify-content: center; }
.contact-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
.profile-grid, .introducer-grid {
    display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;
}
.profile-card, .introducer-card {
    padding: 14px; border-radius: 18px; border: 1px solid var(--line);
    background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
}
.profile-card small, .introducer-card small {
    display: block; color: var(--muted); font-size: 12px; margin-bottom: 6px;
}
.profile-card strong, .introducer-card strong {
    display: block; font-size: 15px; line-height: 1.35; color: var(--text); word-break: break-word;
}
.details { display: grid; gap: 12px; margin-top: 4px; }
.detail-item { padding: 12px 0; border-top: 1px solid var(--line); }
.detail-item:first-child { border-top: 0; padding-top: 0; }
.detail-label {
    display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px;
    text-transform: uppercase; letter-spacing: 0.06em; font-weight: 700;
}
.detail-value { margin: 0; color: var(--text); font-size: 15px; line-height: 1.5; word-break: break-word; }
.detail-value a { color: var(--primary); text-decoration: none; font-weight: 700; }
.register-cta {
    margin-top: 16px;
    background: linear-gradient(135deg, #fff5d8 0%, #f3cd71 48%, #c99d34 100%);
    border-radius: 24px; padding: 18px;
    box-shadow: 0 18px 34px rgba(201,165,76,0.28);
    border: 1px solid rgba(201,165,76,0.28);
    position: relative; overflow: hidden;
}
.register-cta::before {
    content: ""; position: absolute; top: -30px; right: -30px;
    width: 120px; height: 120px; border-radius: 999px;
    background: rgba(255,255,255,0.22);
}
.register-cta-content { position: relative; z-index: 1; text-align: center; }
.register-cta h2 { margin: 0 0 8px; font-size: 22px; line-height: 1.2; color: #533d08; }
.register-cta p { margin: 0 0 14px; font-size: 14px; line-height: 1.5; color: #6c5315; }
.register-btn {
    display: flex; align-items: center; justify-content: center;
    width: 100%; min-height: 56px; padding: 0 20px; border-radius: 999px;
    background: linear-gradient(135deg, #173153 0%, #10233b 100%);
    color: #fff; font-size: 17px; font-weight: 800; letter-spacing: 0.02em;
    box-shadow: 0 12px 24px rgba(16,35,59,0.22), 0 0 0 8px rgba(255,255,255,0.18);
    animation: pulseGlow 1.8s infinite;
    text-decoration: none;
}
.register-btn:hover { transform: translateY(-1px) scale(1.01); color: #fff; }
@keyframes pulseGlow {
    0%   { box-shadow: 0 12px 24px rgba(16,35,59,0.22), 0 0 0 0 rgba(23,49,83,0.18); }
    70%  { box-shadow: 0 12px 24px rgba(16,35,59,0.22), 0 0 0 14px rgba(23,49,83,0); }
    100% { box-shadow: 0 12px 24px rgba(16,35,59,0.22), 0 0 0 0 rgba(23,49,83,0); }
}
.footer-note { text-align: center; color: var(--muted); font-size: 12px; margin-top: 18px; padding-bottom: 10px; }
.sticky-register-wrap {
    position: fixed; left: 0; right: 0; bottom: 0; z-index: 40;
    padding: 12px 16px calc(12px + env(safe-area-inset-bottom));
    background: linear-gradient(180deg, rgba(245,247,251,0) 0%, rgba(245,247,251,0.95) 36%, rgba(245,247,251,1) 100%);
}
.sticky-register-inner { max-width: 760px; margin: 0 auto; }
.sticky-register-btn {
    display: flex; align-items: center; justify-content: center;
    width: 100%; min-height: 58px; border-radius: 999px;
    background: linear-gradient(135deg, #c99d34 0%, #f1cb67 100%);
    color: #173153; font-size: 18px; font-weight: 900; letter-spacing: 0.02em;
    box-shadow: 0 12px 28px rgba(201,165,76,0.34), 0 0 0 6px rgba(255,255,255,0.78);
    animation: pulseGlowGold 1.8s infinite;
    text-decoration: none;
}
.sticky-register-btn:hover { color: #173153; }
@keyframes pulseGlowGold {
    0%   { box-shadow: 0 12px 28px rgba(201,165,76,0.34), 0 0 0 0 rgba(201,165,76,0.28); }
    70%  { box-shadow: 0 12px 28px rgba(201,165,76,0.34), 0 0 0 14px rgba(201,165,76,0); }
    100% { box-shadow: 0 12px 28px rgba(201,165,76,0.34), 0 0 0 0 rgba(201,165,76,0); }
}
@media (max-width: 420px) {
    .profile-grid, .introducer-grid { grid-template-columns: 1fr; }
}
/* Extra bottom padding so content isn't hidden by sticky strip */
.app-shell .screen { padding-bottom: 96px; }
</style>
@endpush

@section('content')

<?php
$parts    = explode(' ', strtoupper(trim($user->fullname)));
$initials = count($parts) >= 2
    ? $parts[0][0] . $parts[count($parts) - 1][0]
    : substr($parts[0], 0, 2);

$waNumber = preg_replace('/\D/', '', $user->phone_number);
?>

{{-- ── Hero ── --}}
<section class="hero">
    <div class="avatar-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240" class="avatar">
            <defs>
                <linearGradient id="ag" x1="0" x2="1" y1="0" y2="1">
                    <stop stop-color="#17365b"/>
                    <stop offset="1" stop-color="#c9a54c"/>
                </linearGradient>
            </defs>
            <rect width="240" height="240" rx="120" fill="url(#ag)"/>
            <text x="50%" y="53%" text-anchor="middle"
                  font-family="Arial, sans-serif" font-size="72" font-weight="700" fill="white">
                {{ $initials }}
            </text>
        </svg>
    </div>

    <h1>{{ strtoupper($user->fullname) }}</h1>
    <p>This profile page is shown when another user scans your QR code inside the app.</p>

    <div class="hero-badges" style="justify-content: center; margin-top: 12px;">
        @if($user->city)
            <span class="chip">📍 {{ $user->city }}</span>
        @endif
        <span class="chip">👤 {{ $user->status ?? 'Member' }}</span>
    </div>

    <div class="action-row">
        <a href="tel:{{ $user->phone_number }}" class="btn btn-secondary">
            📞 Call
        </a>
        <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="btn btn-success">
            💬 WhatsApp
        </a>
        <a href="mailto:{{ $user->email }}" class="btn btn-secondary">
            ✉️ Email
        </a>
    </div>
</section>

{{-- ── Register CTA ── --}}
<div class="register-cta">
    <div class="register-cta-content">
        <h2>Interested to Join?</h2>
        <p>Register now and start your journey with us today.</p>
        <a href="#" class="register-btn">🌟 Register Now</a>
    </div>
</div>

{{-- ── Profile information card ── --}}
<div class="card">
    <div class="section-head">
        <div>
            <h2>Profile Information</h2>
        </div>
    </div>
    <div class="profile-grid">
        <div class="profile-card">
            <small>Full Name</small>
            <strong>{{ $user->fullname }}</strong>
        </div>
        <div class="profile-card">
            <small>Email</small>
            <strong>{{ $user->email }}</strong>
        </div>
        <div class="profile-card">
            <small>Contact Number</small>
            <strong>{{ $user->phone_number }}</strong>
        </div>
        <div class="profile-card">
            <small>City</small>
            <strong>{{ $user->city ?? '—' }}</strong>
        </div>
    </div>
</div>

{{-- ── Introducer card (only if introducer is present) ── --}}
@if($user->introducer_name)
<div class="card">
    <div class="section-head">
        <div>
            <h2>Introducer</h2>
        </div>
    </div>
    <div class="introducer-grid">
        <div class="introducer-card">
            <small>Name</small>
            <strong>{{ $user->introducer_name }}</strong>
        </div>
        @if($user->introducer_email)
        <div class="introducer-card">
            <small>Email</small>
            <strong>{{ $user->introducer_email }}</strong>
        </div>
        @endif
        @if($user->introducer_phone)
        <div class="introducer-card" style="grid-column: span 2;">
            <small>Contact Number</small>
            <strong>{{ $user->introducer_phone }}</strong>
        </div>
        @endif
    </div>
    <div class="contact-actions">
        @if($user->introducer_phone)
        <a href="tel:{{ $user->introducer_phone }}" class="btn btn-secondary">
            📞 Call Introducer
        </a>
        @endif
        @if($user->introducer_email)
        <a href="mailto:{{ $user->introducer_email }}" class="btn btn-secondary">
            ✉️ Email Introducer
        </a>
        @endif
    </div>
</div>
@endif

{{-- ── Quick view card ── --}}
<div class="card">
    <div class="section-head">
        <div>
            <h2>Quick View</h2>
        </div>
    </div>
    <div class="details">
        <div class="detail-item">
            <span class="detail-label">Scanned Profile</span>
            <p class="detail-value">{{ $user->fullname }}</p>
        </div>
        <div class="detail-item">
            <span class="detail-label">Primary Contact</span>
            <p class="detail-value">
                <a href="tel:{{ $user->phone_number }}">{{ $user->phone_number }}</a>
                &nbsp;·&nbsp;
                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
            </p>
        </div>
        <div class="detail-item">
            <span class="detail-label">Location</span>
            <p class="detail-value">{{ $user->city ?? '—' }}</p>
        </div>
        @if($user->introducer_name)
        <div class="detail-item">
            <span class="detail-label">Introducer</span>
            <p class="detail-value">
                {{ $user->introducer_name }}
                @if($user->introducer_phone)
                    &nbsp;·&nbsp;
                    <a href="tel:{{ $user->introducer_phone }}">{{ $user->introducer_phone }}</a>
                @endif
            </p>
        </div>
        @endif
    </div>
</div>

{{-- ── Footer note ── --}}
<p class="footer-note">MEEM Gold &copy; {{ date('Y') }} · Member Profile</p>

{{-- ── Sticky register strip ── --}}
<div class="sticky-register-wrap">
    <div class="sticky-register-inner">
        <a href="#" class="sticky-register-btn">🌟 Register Now</a>
    </div>
</div>

@endsection
