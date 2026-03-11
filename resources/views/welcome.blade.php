<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Meem Event') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <h1 class="fw-bold mb-3">{{ config('app.name', 'Meem Event') }}</h1>
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="btn btn-dark">Admin Login</a>
            @endif
        </div>
    </div>
</body>
</html>
