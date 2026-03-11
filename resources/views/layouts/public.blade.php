<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Meem Event') }} - @yield('title', 'Check-in')</title>
    {{-- Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ── Design system: MEEM Gold ── */
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --text: #172033;
            --muted: #6f7a8a;
            --line: #e8edf4;
            --gold: #c9a54c;
            --gold-dark: #9e7b27;
            --primary: #173153;
            --success: #1f8b5b;
            --success-soft: #e9f7f0;
            --shadow: 0 10px 30px rgba(23, 49, 83, 0.08);
            --radius: 22px;
        }

        body {
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: radial-gradient(circle at top right, rgba(201,165,76,0.18), transparent 28%),
                        linear-gradient(180deg, #f8fafc 0%, #f3f6fb 100%);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            margin: 0;
        }

        /* ── Topbar ── */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            padding: 14px 16px 12px;
            backdrop-filter: blur(14px);
            background: rgba(245, 247, 251, 0.86);
            border-bottom: 1px solid rgba(232, 237, 244, 0.8);
        }
        .topbar-row {
            max-width: 760px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-badge {
            width: 42px; height: 42px; border-radius: 14px;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold));
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            box-shadow: 0 8px 22px rgba(201,165,76,0.28);
            flex-shrink: 0;
        }
        .brand-text small { display: block; color: var(--muted); font-size: 12px; margin-bottom: 2px; }
        .brand-text strong { display: block; font-size: 16px; line-height: 1.2; color: var(--text); }

        /* ── App shell ── */
        .app-shell { min-height: 100vh; }
        .screen { width: 100%; max-width: 760px; margin: 0 auto; padding: 16px 16px 32px; }
        @media (min-width: 640px) { .screen { padding: 20px 20px 36px; } }

        /* ── Hero ── */
        .hero {
            position: relative; overflow: hidden; border-radius: 28px;
            padding: 22px 18px 18px;
            background: linear-gradient(160deg, #18365b 0%, #10233b 100%);
            color: #fff;
            box-shadow: var(--shadow);
        }
        .hero::after {
            content: ""; position: absolute; width: 220px; height: 220px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(201,165,76,.45) 0%, rgba(201,165,76,0) 72%);
            right: -56px; top: -56px;
        }
        .hero > * { position: relative; z-index: 1; }
        .hero h1 { margin: 14px 0 0; font-size: 28px; line-height: 1.15; letter-spacing: -.02em; }
        .hero p  { margin: 10px 0 0; color: rgba(255,255,255,.82); font-size: 14px; line-height: 1.6; }
        @media (min-width: 768px) { .hero { padding: 26px 24px 22px; } .hero h1 { font-size: 34px; } }

        /* ── Hero badges / chips ── */
        .hero-badges, .metric-row { display: flex; flex-wrap: wrap; gap: 8px; }
        .chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 10px; border-radius: 999px;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.12);
            font-size: 12px; font-weight: 700;
        }
        .metric-row { margin-top: 16px; }
        .metric {
            min-width: 112px; padding: 12px 14px; border-radius: 18px;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.1);
        }
        .metric strong { display: block; font-size: 18px; line-height: 1.2; margin-bottom: 4px; }
        .metric span   { font-size: 12px; color: rgba(255,255,255,.74); }

        /* ── Cards ── */
        .card {
            background: var(--card);
            border: 1px solid rgba(232,237,244,.84) !important;
            border-radius: var(--radius) !important;
            box-shadow: var(--shadow);
            padding: 18px;
            margin-top: 16px;
        }

        /* ── Section head ── */
        .section-head {
            display: flex; align-items: flex-start; justify-content: space-between;
            gap: 12px; margin-bottom: 14px;
        }
        .section-head h2 { margin: 0; font-size: 18px; line-height: 1.2; }
        .section-head p  { margin: 6px 0 0; color: var(--muted); font-size: 13px; line-height: 1.55; }

        /* ── Pills ── */
        .count-pill, .tag-pill {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 7px 11px; border-radius: 999px; font-size: 12px; font-weight: 700;
            white-space: nowrap; color: var(--gold-dark); background: #f9f2df;
        }

        /* ── Info grid ── */
        .info-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .info-card {
            padding: 14px; border-radius: 18px; border: 1px solid var(--line);
            background: linear-gradient(180deg, #fff 0%, #f9fbfe 100%);
        }
        .info-card-full { grid-column: span 2; }
        .info-card small  { display: block; color: var(--muted); font-size: 12px; margin-bottom: 6px; }
        .info-card strong { display: block; font-size: 15px; line-height: 1.3; color: var(--text); }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            min-height: 44px; padding: 0 16px; border-radius: 999px; border: 0;
            font-size: 14px; font-weight: 700; cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease;
            text-decoration: none;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(23,49,83,.12); }
        .btn-primary   { color: #fff; background: linear-gradient(135deg, var(--gold-dark), var(--gold)); }
        .btn-secondary { color: var(--primary); background: rgba(23,49,83,.08); }
        .btn-success   { color: #0c5b3a; background: var(--success-soft); }
        .btn-submit    { width: 100%; min-height: 52px; font-size: 15px; border-radius: 16px; }

        /* ── Form fields ── */
        .field { display: grid; gap: 8px; margin-bottom: 16px; }
        .field-label { font-size: 12px; font-weight: 700; color: var(--primary); letter-spacing: .01em; }
        .control {
            display: flex; align-items: center; gap: 10px; min-height: 52px; padding: 0 14px;
            border-radius: 16px; border: 1px solid var(--line); background: #fff;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
        }
        .control span  { font-size: 18px; line-height: 1; opacity: .72; flex-shrink: 0; }
        .control input {
            width: 100%; min-width: 0; border: 0; outline: 0; background: transparent;
            color: var(--text); font-size: 15px; font-family: inherit;
        }

        /* ── User option ── */
        .user-option {
            display: flex; align-items: center; gap: 12px; padding: 12px 14px;
            border-radius: 16px; border: 1px solid var(--line);
            background: linear-gradient(180deg, #fff 0%, #f9fbfe 100%);
            cursor: pointer; margin-bottom: 8px;
            transition: border-color .15s, background .15s;
        }
        .user-option input[type=radio] {
            accent-color: var(--gold-dark); flex-shrink: 0; width: 18px; height: 18px;
        }
        .user-name { font-size: 15px; font-weight: 700; color: var(--text); }
        .user-meta { font-size: 13px; color: var(--muted); margin-top: 3px; }
        .code-badge {
            display: inline-flex; padding: 3px 8px; border-radius: 999px;
            background: #f9f2df; color: var(--gold-dark); font-size: 12px; font-weight: 700;
            margin-right: 6px;
        }
        .user-option.selected {
            border-color: var(--gold);
            background: linear-gradient(180deg, #fdf8ec 0%, #fdf3da 100%);
        }

        /* ── User list scroll container ── */
        #user-list {
            max-height: 340px; overflow-y: auto; padding-right: 4px;
        }
        #user-list::-webkit-scrollbar { width: 4px; }
        #user-list::-webkit-scrollbar-track { background: transparent; }
        #user-list::-webkit-scrollbar-thumb { background: var(--line); border-radius: 999px; }

        /* ── Error ── */
        .error-msg { color: #c0392b; font-size: 13px; margin-top: 6px; }

        /* ── Action row ── */
        .action-row { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; }
    </style>
    @stack('styles')
</head>
<body>

<header class="topbar">
    <div class="topbar-row">
        <div class="brand">
            <div class="brand-badge">
                <i class="bi bi-calendar-event"></i>
            </div>
            <div class="brand-text">
                <small>Check-in Portal</small>
                <strong>{{ config('app.name', 'Meem Event') }}</strong>
            </div>
        </div>
    </div>
</header>

<div class="app-shell">
    <div class="screen">
        @yield('content')
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
