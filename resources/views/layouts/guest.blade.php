<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Meem Event') }}</title>
    {{-- Dashlite CSS --}}
    <link rel="stylesheet" href="{{ asset('dashlite/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('dashlite/css/theme.css') }}">
</head>
<body class="nk-body bg-white npc-general pg-auth">
    <div class="nk-app-root">
        <div class="nk-main">
            <div class="nk-wrap nk-wrap-nosidebar">
                <div class="nk-content">
                    <div class="nk-split nk-split-page nk-split-md">
                        <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-white w-lg-45">
                            <div class="nk-block nk-block-middle nk-auth-body">
                                <div class="brand-logo pb-5">
                                    <a href="/" class="logo-link">
                                        <img class="logo-light logo-img logo-img-lg" src="{{ asset('assets/icons/logo-transparent-192x192.png') }}" alt="logo" style="max-height:50px;">
                                        <img class="logo-dark logo-img logo-img-lg" src="{{ asset('assets/icons/logo-transparent-192x192.png') }}" alt="logo-dark" style="max-height:50px;">
                                    </a>
                                </div>
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h5 class="nk-block-title">{{ config('app.name', 'Meem Event') }}</h5>
                                        <div class="nk-block-des">
                                            <p>Administration Portal</p>
                                        </div>
                                    </div>
                                </div>
                                {{ $slot }}
                            </div>
                            <div class="nk-block nk-auth-footer">
                                <div class="nk-block-between">
                                    <ul class="nav nav-sm">
                                        <li class="nav-item">
                                            <span class="text-soft small">&copy; {{ date('Y') }} {{ config('app.name', 'Meem Event') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="nk-split-content nk-split-stretch bg-lighter d-flex toggle-break-lg toggle-slide toggle-slide-right" data-toggle-body="true" data-content="athPromo" data-toggle-screen="lg" data-toggle-overlay="true">
                            <div class="w-100 d-flex flex-column justify-content-center align-items-center">
                                <div class="text-center p-4">
                                    <img src="{{ asset('assets/icons/logo-transparent-192x192.png') }}" alt="" style="max-width: 200px; opacity:.8;">
                                    <h3 class="title mt-4">{{ config('app.name', 'Meem Event') }}</h3>
                                    <p class="text-soft">Administration &amp; Event Management System</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Dashlite bundle (includes jQuery & Bootstrap) --}}
    <script src="{{ asset('dashlite/js/bundle.js') }}"></script>
    <script src="{{ asset('dashlite/js/scripts.js') }}"></script>
</body>
</html>
