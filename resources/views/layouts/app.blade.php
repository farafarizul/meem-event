<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Meem Event') }} - @yield('title', 'Admin')</title>

    {{-- Dashlite CSS --}}
    <link rel="stylesheet" href="{{ asset('dashlite/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('dashlite/css/theme.css') }}">
    {{-- Bootstrap Icons (used in page content) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- DataTables Bootstrap 5 CSS (matches version bundled in bundle.js) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    @stack('styles')
</head>

<body class="nk-body bg-lighter npc-general has-sidebar">
<div class="nk-app-root">
    <div class="nk-main">

        {{-- ===== SIDEBAR ===== --}}
        <div class="nk-sidebar nk-sidebar-fixed is-dark" data-content="sidebarMenu">
            <div class="nk-sidebar-element nk-sidebar-head">
                <div class="nk-menu-trigger">
                    <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu">
                        <em class="icon ni ni-arrow-left"></em>
                    </a>
                    <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu">
                        <em class="icon ni ni-menu"></em>
                    </a>
                </div>
                <div class="nk-sidebar-brand">
                    <a href="{{ route('admin.dashboard') }}" class="logo-link nk-sidebar-logo">
                        <img class="logo-light logo-img" src="{{ asset('assets/icons/logo-transparent-192x192.png') }}" alt="logo" style="max-height:32px;">
                        <img class="logo-dark logo-img" src="{{ asset('assets/icons/logo-transparent-192x192.png') }}" alt="logo-dark" style="max-height:32px;">
                        <span class="nio-version">{{ config('app.name', 'Meem Event') }}</span>
                    </a>
                </div>
            </div>

            <div class="nk-sidebar-element nk-sidebar-body">
                <div class="nk-sidebar-content">
                    <div class="nk-sidebar-menu" data-simplebar>
                        <ul class="nk-menu">
                            <li class="nk-menu-heading">
                                <h6 class="overline-title text-primary-alt">Main Menu</h6>
                            </li>
                            <li class="nk-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <a href="{{ route('admin.dashboard') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-home-alt"></em></span>
                                    <span class="nk-menu-text">Dashboard</span>
                                </a>
                            </li>
                            <li class="nk-menu-item {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.branches.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-building"></em></span>
                                    <span class="nk-menu-text">Branches</span>
                                </a>
                            </li>
                            <li class="nk-menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.users.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                    <span class="nk-menu-text">Users</span>
                                </a>
                            </li>
                            <li class="nk-menu-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.events.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-calendar"></em></span>
                                    <span class="nk-menu-text">Events</span>
                                </a>
                            </li>
                            <li class="nk-menu-item {{ request()->routeIs('admin.checkins.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.checkins.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-check-circle"></em></span>
                                    <span class="nk-menu-text">Check-ins</span>
                                </a>
                            </li>
                            <li class="nk-menu-item {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.logs.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-activity-alt"></em></span>
                                    <span class="nk-menu-text">Logs</span>
                                </a>
                            </li>
                            <li class="nk-menu-heading">
                                <h6 class="overline-title text-primary-alt">Price Management</h6>
                            </li>
                            <li class="nk-menu-item {{ request()->routeIs('admin.gold-price.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.gold-price.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-trend-up"></em></span>
                                    <span class="nk-menu-text">Gold Price</span>
                                </a>
                            </li>
                            <li class="nk-menu-item {{ request()->routeIs('admin.silver-price.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.silver-price.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                                    <span class="nk-menu-text">Silver Price</span>
                                </a>
                            </li>
                            <li class="nk-menu-heading">
                                <h6 class="overline-title text-primary-alt">Settings</h6>
                            </li>
                            <li class="nk-menu-item {{ request()->routeIs('admin.apk-detail.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.apk-detail.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-mobile"></em></span>
                                    <span class="nk-menu-text">APK Management</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        {{-- ===== END SIDEBAR ===== --}}

        {{-- ===== MAIN CONTENT WRAPPER ===== --}}
        <div class="nk-wrap">

            {{-- Header --}}
            <div class="nk-header nk-header-fixed is-light">
                <div class="container-fluid">
                    <div class="nk-header-wrap">
                        <div class="nk-menu-trigger d-xl-none ms-n1">
                            <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu">
                                <em class="icon ni ni-menu"></em>
                            </a>
                        </div>
                        <div class="nk-header-brand d-xl-none">
                            <a href="{{ route('admin.dashboard') }}" class="logo-link">
                                <img class="logo-light logo-img" src="{{ asset('assets/icons/logo-transparent-192x192.png') }}" alt="logo" style="max-height:30px;">
                            </a>
                        </div>

                        <div class="nk-header-tools">
                            <ul class="nk-quick-nav">
                                <li class="dropdown user-dropdown">
                                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                        <div class="user-toggle">
                                            <div class="user-avatar sm">
                                                <em class="icon ni ni-user-alt"></em>
                                            </div>
                                            <div class="user-info d-none d-md-block">
                                                <div class="user-status">Administrator</div>
                                                <div class="user-name dropdown-indicator">{{ Auth::user()->fullname }}</div>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1">
                                        <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                            <div class="user-card">
                                                <div class="user-avatar">
                                                    <span>{{ strtoupper(substr(Auth::user()->fullname, 0, 2)) }}</span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="lead-text">{{ Auth::user()->fullname }}</span>
                                                    <span class="sub-text">{{ Auth::user()->email }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dropdown-inner">
                                            <ul class="link-list">
                                                <li>
                                                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                                        @csrf
                                                    </form>
                                                    <a href="#" onclick="document.getElementById('logout-form').submit(); return false;">
                                                        <em class="icon ni ni-signout"></em><span>Sign out</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Header --}}

            {{-- Page Content --}}
            <div class="nk-content">
                <div class="container-fluid">
                    <div class="nk-content-inner">
                        <div class="nk-content-body">

                            @if (isset($header))
                                <div class="nk-block-head nk-block-head-sm">
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">{{ $header }}</h3>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <em class="icon ni ni-check-circle me-1"></em>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <em class="icon ni ni-alert-circle me-1"></em>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{ $slot }}

                        </div>
                    </div>
                </div>
            </div>
            {{-- End Page Content --}}

            {{-- Footer --}}
            <div class="nk-footer">
                <div class="container-fluid">
                    <div class="nk-footer-wrap">
                        <div class="nk-footer-copyright">
                            &copy; {{ date('Y') }} <strong>{{ config('app.name', 'Meem Event') }}</strong>. All Rights Reserved.
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Footer --}}

        </div>
        {{-- ===== END MAIN CONTENT WRAPPER ===== --}}

    </div>
</div>

{{-- Dashlite bundle (includes jQuery, Bootstrap, DataTables 1.x & SimpleBar) --}}
<script src="{{ asset('dashlite/js/bundle.js') }}"></script>
{{-- Dashlite scripts (sidebar toggle, dropdowns, etc.) --}}
<script src="{{ asset('dashlite/js/scripts.js') }}"></script>

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
