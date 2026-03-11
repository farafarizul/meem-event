<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Meem Event') }} - @yield('title', 'Admin')</title>

    {{-- Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- DataTables + Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">

    <style>
        .sidebar { min-height: calc(100vh - 56px); }
        .sidebar .nav-link { color: #495057; border-radius: 0.375rem; }
        .sidebar .nav-link:hover { background-color: #e9ecef; }
        .sidebar .nav-link.active { background-color: #212529; color: #fff !important; }
        .sidebar .nav-link i { width: 1.2rem; }
    </style>

    @stack('styles')
</head>
<body class="bg-light">

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/icons/logo-transparent-192x192.png') }}" style="width: 30px; height: 30px; object-fit: contain;" alt="Meem Logo">{{ config('app.name', 'Meem Event') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->fullname }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        {{-- Sidebar --}}
        <nav class="col-md-2 d-none d-md-block bg-white sidebar py-3 border-end shadow-sm">
            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                       href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people me-2"></i>Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}"
                       href="{{ route('admin.events.index') }}">
                        <i class="bi bi-calendar2-event me-2"></i>Events
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.checkins.*') ? 'active' : '' }}"
                       href="{{ route('admin.checkins.index') }}">
                        <i class="bi bi-check2-circle me-2"></i>Check-ins
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}"
                       href="{{ route('admin.logs.index') }}">
                        <i class="bi bi-check2-circle me-2"></i>Logs
                    </a>
                </li>
            </ul>
        </nav>

        {{-- Main Content --}}
        <main class="col-md-10 ms-sm-auto px-4 py-4">
            @if (isset($header))
                <h4 class="mb-3 fw-semibold">{{ $header }}</h4>
                <hr class="mb-4">
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{ $slot }}
        </main>

    </div>
</div>

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
{{-- Bootstrap 5 JS Bundle --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- DataTables --}}
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Global AJAX CSRF header
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Auto-dismiss alerts after 5s
    setTimeout(function () {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);
</script>

@stack('scripts')
</body>
</html>
