<x-guest-layout>
    <div class="mb-3 text-muted small">
        Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-3">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-dark btn-sm">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link btn-sm text-muted text-decoration-none">Log Out</button>
        </form>
    </div>
</x-guest-layout>
