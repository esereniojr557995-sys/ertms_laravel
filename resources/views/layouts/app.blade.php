<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ERTMS') — Emergency Response System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700&family=Geist+Mono:wght@400;500&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        :root {
            --bg: #05080d;
            --surface: #080e18;
            --surface2: #0c1522;
            --surface3: #111d2e;
            --surface4: #162236;
            --border: #172030;
            --border2: #1e2d42;
            --border3: #253648;

            --text: #c8d8e8;
            --text-bright: #e8f0f8;
            --text-muted: #4a6480;
            --text-dim: #2d4257;

            --accent: #f04923;
            --accent-dim: rgba(240,73,35,.12);
            --accent-glow: rgba(240,73,35,.2);
            --accent2: #f5724a;
            --accent3: #ff9472;

            --blue: #3a8eff;
            --blue-dim: rgba(58,142,255,.1);
            --green: #1ec96d;
            --green-dim: rgba(30,201,109,.08);
            --yellow: #e8a825;
            --yellow-dim: rgba(232,168,37,.1);
            --red: #e84545;
            --red-dim: rgba(232,69,69,.1);
            --purple: #8b7cf6;
            --purple-dim: rgba(139,124,246,.1);

            --sidebar-w: 240px;
            --topbar-h: 52px;
            --r: 8px;
            --r-sm: 5px;
            --r-lg: 12px;

            --font: 'Geist', 'DM Sans', system-ui, sans-serif;
            --font-mono: 'Geist Mono', monospace;
            --font-display: 'Geist', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { font-size: 13px; -webkit-font-smoothing: antialiased; }
        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            overflow: hidden;
        }

        /* ── SIDEBAR ─────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
        }

        .sb-brand {
            padding: 16px 16px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .sb-brand-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--accent), #c93a18);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 0 1px rgba(240,73,35,.3), 0 4px 12px rgba(240,73,35,.25);
            flex-shrink: 0;
        }
        .sb-brand-text .name {
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .04em;
            color: var(--text-bright);
            line-height: 1;
        }
        .sb-brand-text .sub {
            font-size: .58rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .14em;
            margin-top: 3px;
            font-weight: 500;
        }

        .sb-user {
            margin: 10px 10px 6px;
            padding: 10px 11px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--r);
            flex-shrink: 0;
        }
        .sb-user .uname {
            font-weight: 600;
            font-size: .8rem;
            color: var(--text-bright);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -.01em;
        }
        .sb-user .urole {
            font-size: .64rem;
            color: var(--text-muted);
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 500;
        }
        .sb-user .ustatus {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 7px;
        }
        .ustatus-dot {
            width: 5px; height: 5px;
            background: var(--green);
            border-radius: 50%;
            box-shadow: 0 0 6px var(--green);
            animation: pulse-dot 2.5s ease-in-out infinite;
        }
        .sb-user .ustatus span {
            font-size: .62rem;
            color: var(--green);
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: .4; }
        }

        .sb-nav {
            flex: 1;
            overflow-y: auto;
            padding: 4px 0 8px;
            scrollbar-width: thin;
            scrollbar-color: var(--border2) transparent;
        }
        .sb-nav::-webkit-scrollbar { width: 3px; }
        .sb-nav::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 2px; }

        .sb-section {
            padding: 14px 16px 5px;
            font-size: .57rem;
            font-weight: 600;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: .16em;
        }

        .sb-nav a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7.5px 11px 7.5px 14px;
            margin: 1px 8px;
            border-radius: var(--r-sm);
            font-size: .79rem;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            position: relative;
            transition: background .1s, color .1s;
            letter-spacing: -.01em;
        }
        .sb-nav a svg { width: 14px; height: 14px; flex-shrink: 0; stroke-width: 2; }
        .sb-nav a:hover { background: var(--surface2); color: var(--text); }
        .sb-nav a.active {
            background: var(--accent-dim);
            color: var(--accent2);
            border: 1px solid rgba(240,73,35,.12);
        }
        .sb-nav a.active svg { color: var(--accent); }
        .sb-nav a .nb {
            margin-left: auto;
            background: var(--red);
            color: #fff;
            font-size: .58rem;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 10px;
            min-width: 16px;
            text-align: center;
            font-family: var(--font-mono);
        }

        .sb-footer {
            padding: 8px 10px 10px;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }
        .sb-footer form button {
            display: flex;
            align-items: center;
            gap: 7px;
            width: 100%;
            padding: 7.5px 11px;
            border-radius: var(--r-sm);
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: .77rem;
            font-family: var(--font);
            cursor: pointer;
            transition: all .12s;
            letter-spacing: -.01em;
        }
        .sb-footer form button svg { width: 13px; height: 13px; }
        .sb-footer form button:hover {
            background: var(--red-dim);
            color: var(--red);
            border-color: rgba(232,69,69,.2);
        }

        /* ── MAIN ────────────────────────────────────────────── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .topbar {
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 22px;
            flex-shrink: 0;
        }
        .topbar-left .page-label {
            font-size: .88rem;
            font-weight: 600;
            color: var(--text-bright);
            letter-spacing: -.02em;
        }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: .68rem;
            font-weight: 600;
            color: var(--green);
            background: var(--green-dim);
            border: 1px solid rgba(30,201,109,.12);
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .topbar-status .pulse {
            width: 5px; height: 5px;
            background: var(--green);
            border-radius: 50%;
            box-shadow: 0 0 5px var(--green);
            animation: pulse-dot 2s infinite;
        }
        .topbar-divider {
            width: 1px; height: 16px;
            background: var(--border2);
        }
        .topbar-clock {
            font-size: .7rem;
            color: var(--text-muted);
            font-family: var(--font-mono);
            letter-spacing: .02em;
        }

        .content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px 22px;
            scrollbar-width: thin;
            scrollbar-color: var(--border2) transparent;
        }
        .content::-webkit-scrollbar { width: 4px; }
        .content::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }

        /* ── FLASH ───────────────────────────────────────────── */
        .flash {
            padding: 9px 13px;
            border-radius: var(--r-sm);
            margin-bottom: 16px;
            font-size: .78rem;
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: 500;
        }
        .flash svg { width: 14px; height: 14px; flex-shrink: 0; }
        .flash-success { background: var(--green-dim); border: 1px solid rgba(30,201,109,.2); color: var(--green); }
        .flash-error { background: var(--red-dim); border: 1px solid rgba(232,69,69,.2); color: var(--red); }
        .flash-warning { background: var(--yellow-dim); border: 1px solid rgba(232,168,37,.2); color: var(--yellow); }

        /* ── PAGE HEADER ─────────────────────────────────────── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 18px;
            gap: 12px;
        }
        .page-header h1 {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text-bright);
            line-height: 1.2;
            letter-spacing: -.03em;
        }
        .page-header .bc {
            font-size: .67rem;
            color: var(--text-muted);
            margin-top: 3px;
            letter-spacing: .02em;
        }

        /* ── STAT CARDS ──────────────────────────────────────── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 16px 16px 14px;
            position: relative;
            overflow: hidden;
            transition: border-color .15s;
        }
        .stat-card:hover { border-color: var(--border2); }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
        }
        .stat-card.red::after { background: linear-gradient(90deg, var(--red), transparent 70%); }
        .stat-card.blue::after { background: linear-gradient(90deg, var(--blue), transparent 70%); }
        .stat-card.green::after { background: linear-gradient(90deg, var(--green), transparent 70%); }
        .stat-card.yellow::after { background: linear-gradient(90deg, var(--yellow), transparent 70%); }
        .stat-card.orange::after { background: linear-gradient(90deg, var(--accent), transparent 70%); }
        .stat-card.purple::after { background: linear-gradient(90deg, var(--purple), transparent 70%); }

        .stat-card .sc-icon {
            width: 26px; height: 26px;
            border-radius: 5px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 12px;
        }
        .stat-card .sc-icon svg { width: 13px; height: 13px; }
        .stat-card.red .sc-icon { background: var(--red-dim); color: var(--red); }
        .stat-card.blue .sc-icon { background: var(--blue-dim); color: var(--blue); }
        .stat-card.green .sc-icon { background: var(--green-dim); color: var(--green); }
        .stat-card.yellow .sc-icon { background: var(--yellow-dim); color: var(--yellow); }
        .stat-card.orange .sc-icon { background: var(--accent-dim); color: var(--accent2); }
        .stat-card.purple .sc-icon { background: var(--purple-dim); color: var(--purple); }

        .stat-card .sc-val {
            font-family: var(--font-mono);
            font-size: 1.75rem;
            font-weight: 500;
            line-height: 1;
            margin-bottom: 5px;
            letter-spacing: -.04em;
        }
        .stat-card.red .sc-val { color: var(--red); }
        .stat-card.blue .sc-val { color: var(--blue); }
        .stat-card.green .sc-val { color: var(--green); }
        .stat-card.yellow .sc-val { color: var(--yellow); }
        .stat-card.orange .sc-val { color: var(--accent2); }
        .stat-card.purple .sc-val { color: var(--purple); }

        .stat-card .sc-label {
            font-size: .65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-muted);
        }
        .stat-card .sc-sub {
            font-size: .67rem;
            color: var(--text-dim);
            margin-top: 2px;
        }

        /* ── CARDS ───────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            overflow: hidden;
        }
        .card-header {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .card-header h2 {
            font-size: .78rem;
            font-weight: 600;
            color: var(--text-bright);
            display: flex;
            align-items: center;
            gap: 6px;
            letter-spacing: -.01em;
        }
        .card-header h2 svg { width: 13px; height: 13px; color: var(--text-muted); }
        .card-body { padding: 16px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
        @media(max-width: 900px) { .grid-2, .grid-3 { grid-template-columns: 1fr; } }

        /* ── TABLES ──────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .78rem; }
        thead th {
            padding: 8px 13px;
            text-align: left;
            font-size: .6rem;
            font-weight: 600;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: .12em;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            background: var(--surface2);
        }
        tbody tr {
            border-bottom: 1px solid rgba(23,32,48,.8);
            transition: background .08s;
        }
        tbody tr:hover { background: rgba(255,255,255,.014); }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 9px 13px; vertical-align: middle; }

        /* ── BADGES ──────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: .6rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .07em;
            white-space: nowrap;
            font-family: var(--font-mono);
        }
        .badge-open      { background: var(--blue-dim);   color: var(--blue);   border: 1px solid rgba(58,142,255,.15); }
        .badge-active    { background: var(--red-dim);    color: var(--red);    border: 1px solid rgba(232,69,69,.15); }
        .badge-contained { background: var(--yellow-dim); color: var(--yellow); border: 1px solid rgba(232,168,37,.15); }
        .badge-closed    { background: var(--green-dim);  color: var(--green);  border: 1px solid rgba(30,201,109,.15); }
        .badge-low       { background: var(--green-dim);  color: var(--green);  border: 1px solid rgba(30,201,109,.15); }
        .badge-moderate  { background: var(--yellow-dim); color: var(--yellow); border: 1px solid rgba(232,168,37,.15); }
        .badge-high      { background: var(--red-dim);    color: var(--red);    border: 1px solid rgba(232,69,69,.15); }
        .badge-critical  { background: rgba(240,73,35,.12); color: var(--accent2); border: 1px solid rgba(240,73,35,.2); }
        .badge-admin     { background: var(--purple-dim); color: var(--purple); border: 1px solid rgba(139,124,246,.15); }
        .badge-commander { background: var(--red-dim);    color: var(--red);    border: 1px solid rgba(232,69,69,.15); }
        .badge-responder { background: var(--blue-dim);   color: var(--blue);   border: 1px solid rgba(58,142,255,.15); }
        .badge-citizen   { background: var(--green-dim);  color: var(--green);  border: 1px solid rgba(30,201,109,.15); }
        .badge-info      { background: var(--blue-dim);   color: var(--blue);   border: 1px solid rgba(58,142,255,.15); }
        .badge-warning   { background: var(--yellow-dim); color: var(--yellow); border: 1px solid rgba(232,168,37,.15); }
        .badge-pending   { background: var(--yellow-dim); color: var(--yellow); border: 1px solid rgba(232,168,37,.15); }
        .badge-in_progress { background: var(--blue-dim); color: var(--blue);   border: 1px solid rgba(58,142,255,.15); }
        .badge-completed { background: var(--green-dim);  color: var(--green);  border: 1px solid rgba(30,201,109,.15); }
        .badge-cancelled { background: rgba(74,100,128,.08); color: var(--text-muted); border: 1px solid rgba(74,100,128,.15); }
        .badge-available { background: var(--green-dim);  color: var(--green);  border: 1px solid rgba(30,201,109,.15); }
        .badge-in_use    { background: var(--accent-dim); color: var(--accent2); border: 1px solid rgba(240,73,35,.15); }
        .badge-maintenance { background: var(--yellow-dim); color: var(--yellow); border: 1px solid rgba(232,168,37,.15); }
        .badge-depleted  { background: var(--red-dim);    color: var(--red);    border: 1px solid rgba(232,69,69,.15); }
        .badge-immediate { background: var(--red-dim);    color: var(--red);    border: 1px solid rgba(232,69,69,.2); }
        .badge-delayed   { background: var(--yellow-dim); color: var(--yellow); border: 1px solid rgba(232,168,37,.15); }
        .badge-minor     { background: var(--green-dim);  color: var(--green);  border: 1px solid rgba(30,201,109,.15); }
        .badge-expectant { background: rgba(74,100,128,.08); color: var(--text-muted); border: 1px solid rgba(74,100,128,.15); }
        .badge-deceased  { background: rgba(23,32,48,.6); color: var(--text-dim); border: 1px solid var(--border); }

        /* ── BUTTONS ─────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6.5px 13px;
            border-radius: var(--r-sm);
            font-size: .76rem;
            font-weight: 600;
            font-family: var(--font);
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all .12s;
            white-space: nowrap;
            letter-spacing: -.01em;
        }
        .btn svg { width: 13px; height: 13px; }
        .btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 1px 0 rgba(0,0,0,.2), 0 0 0 1px rgba(240,73,35,.5);
        }
        .btn-primary:hover {
            background: #d93e1c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(240,73,35,.3), 0 0 0 1px rgba(240,73,35,.4);
        }
        .btn-secondary {
            background: var(--surface2);
            color: var(--text);
            border: 1px solid var(--border2);
        }
        .btn-secondary:hover { background: var(--surface3); }
        .btn-danger {
            background: var(--red-dim);
            color: var(--red);
            border: 1px solid rgba(232,69,69,.2);
        }
        .btn-danger:hover { background: rgba(232,69,69,.15); }
        .btn-success {
            background: var(--green-dim);
            color: var(--green);
            border: 1px solid rgba(30,201,109,.2);
        }
        .btn-success:hover { background: rgba(30,201,109,.14); }
        .btn-sm { padding: 5px 10px; font-size: .72rem; }
        .btn-xs { padding: 3px 7px; font-size: .68rem; }

        /* ── FORMS ───────────────────────────────────────────── */
        .form-group { margin-bottom: 13px; }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: .67rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .09em;
        }
        .form-control {
            width: 100%;
            padding: 7.5px 10px;
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: var(--r-sm);
            color: var(--text);
            font-size: .8rem;
            font-family: var(--font);
            transition: border-color .12s, box-shadow .12s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(240,73,35,.08);
        }
        .form-control option { background: var(--surface2); }
        textarea.form-control { resize: vertical; min-height: 80px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 13px; }
        @media(max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
        .form-check { display: flex; align-items: center; gap: 6px; font-size: .79rem; }
        .form-check input[type=checkbox] { width: 13px; height: 13px; accent-color: var(--accent); }
        .filter-bar { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; }
        .filter-bar .form-control { width: auto; padding: 5.5px 8px; }

        /* ── MODALS ──────────────────────────────────────────── */
        .modal-backdrop {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.75);
            backdrop-filter: blur(6px);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }
        .modal-backdrop.open { display: flex; }
        .modal {
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: var(--r-lg);
            width: 90%; max-width: 500px;
            max-height: 88vh;
            overflow-y: auto;
            box-shadow: 0 32px 80px rgba(0,0,0,.7), 0 0 0 1px rgba(255,255,255,.03);
        }
        .modal-header {
            padding: 15px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-header h3 {
            font-size: .85rem;
            font-weight: 600;
            color: var(--text-bright);
            letter-spacing: -.02em;
        }
        .modal-close {
            background: none; border: none;
            color: var(--text-muted);
            cursor: pointer; padding: 3px;
            border-radius: 4px;
            transition: color .1s, background .1s;
        }
        .modal-close svg { width: 15px; height: 15px; }
        .modal-close:hover { color: var(--text); background: var(--surface2); }
        .modal-body { padding: 18px; }
        .modal-footer {
            padding: 13px 18px;
            border-top: 1px solid var(--border);
            display: flex; gap: 7px;
            justify-content: flex-end;
        }

        /* ── PROGRESS ────────────────────────────────────────── */
        .progress-bar {
            height: 4px;
            background: var(--surface3);
            border-radius: 3px;
            overflow: hidden;
        }
        .progress-bar .fill { height: 100%; border-radius: 3px; transition: width .3s ease; }
        .progress-bar .fill.green  { background: var(--green); }
        .progress-bar .fill.yellow { background: var(--yellow); }
        .progress-bar .fill.red    { background: var(--red); }
        .progress-bar .fill.blue   { background: var(--blue); }

        /* ── ALERT ROWS ──────────────────────────────────────── */
        .alert-row {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(23,32,48,.7);
        }
        .alert-row:last-child { border-bottom: none; }
        .alert-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 5px;
        }
        .alert-dot.info     { background: var(--blue); }
        .alert-dot.warning  { background: var(--yellow); }
        .alert-dot.high     { background: var(--red); }
        .alert-dot.critical { background: var(--accent); box-shadow: 0 0 6px var(--accent); }
        .alert-title { font-weight: 500; font-size: .79rem; color: var(--text); letter-spacing: -.01em; }
        .alert-meta  { font-size: .67rem; color: var(--text-muted); margin-top: 2px; }

        /* ── INC ICONS ───────────────────────────────────────── */
        .inc-icon {
            width: 28px; height: 28px;
            border-radius: var(--r-sm);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .inc-icon svg { width: 13px; height: 13px; }
        .inc-icon.fire      { background: var(--red-dim);    color: var(--red); }
        .inc-icon.flood     { background: var(--blue-dim);   color: var(--blue); }
        .inc-icon.earthquake { background: var(--yellow-dim); color: var(--yellow); }
        .inc-icon.medical   { background: var(--green-dim);  color: var(--green); }
        .inc-icon.rescue    { background: var(--blue-dim);   color: var(--blue); }
        .inc-icon.hazmat    { background: var(--accent-dim); color: var(--accent2); }
        .inc-icon.wind      { background: var(--purple-dim); color: var(--purple); }
        .inc-icon.other     { background: rgba(74,100,128,.08); color: var(--text-muted); }

        /* ── PAGINATION ──────────────────────────────────────── */
        .pagination { display: flex; gap: 3px; justify-content: center; flex-wrap: wrap; }
        .pagination a, .pagination span {
            padding: 4px 9px;
            border-radius: 4px;
            font-size: .72rem;
            text-decoration: none;
            font-family: var(--font-mono);
        }
        .pagination a {
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text-muted);
            transition: all .1s;
        }
        .pagination a:hover { color: var(--text); border-color: var(--border2); }
        .pagination span.active {
            background: var(--accent);
            color: #fff;
            font-weight: 700;
        }

        /* ── MISC ────────────────────────────────────────────── */
        .warning-strip {
            background: rgba(240,73,35,.06);
            border: 1px solid rgba(240,73,35,.15);
            border-radius: var(--r-sm);
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: .76rem;
            color: var(--accent2);
            margin-bottom: 14px;
        }
        .warning-strip svg { width: 13px; height: 13px; flex-shrink: 0; }

        .empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); }
        .empty-state svg { width: 32px; height: 32px; opacity: .15; margin: 0 auto 10px; display: block; }
        .empty-state p { font-size: .79rem; }

        #map { height: 420px; border-radius: var(--r); overflow: hidden; border: 1px solid var(--border); }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(23,32,48,.6);
            font-size: .78rem;
            gap: 12px;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-row .dl { color: var(--text-muted); flex-shrink: 0; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; font-weight: 500; }
        .detail-row .dv { font-weight: 500; text-align: right; }

        .info-box {
            padding: 9px 12px;
            border-radius: var(--r-sm);
            font-size: .77rem;
            display: flex;
            align-items: flex-start;
            gap: 7px;
            margin-bottom: 14px;
        }
        .info-box svg { width: 13px; height: 13px; flex-shrink: 0; margin-top: 1px; }
        .info-box.blue   { background: var(--blue-dim);   border: 1px solid rgba(58,142,255,.14);  color: #7bbdff; }
        .info-box.yellow { background: var(--yellow-dim); border: 1px solid rgba(232,168,37,.16);  color: #e8c060; }
        .info-box.red    { background: var(--red-dim);    border: 1px solid rgba(232,69,69,.16);   color: #ff7878; }
        .info-box.green  { background: var(--green-dim);  border: 1px solid rgba(30,201,109,.16);  color: #50e09a; }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }
    </style>
    @stack('styles')
</head>
<body>
<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-brand-icon">
            <i data-lucide="shield-alert" style="width:15px;height:15px;color:#fff"></i>
        </div>
        <div class="sb-brand-text">
            <div class="name">ERTMS</div>
            <div class="sc-sub">Emergency Response</div>
        </div>
    </div>

    <div class="sb-user">
        <div class="uname">{{ auth()->user()->name }}</div>
        <div class="urole">{{ auth()->user()->getRoleLabel() }}</div>
        <div class="ustatus">
            <div class="ustatus-dot"></div>
            <span>Online</span>
        </div>
    </div>

    <nav class="sb-nav">
        @yield('sidebar-nav')
    </nav>

    <div class="sb-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">
                <i data-lucide="log-out" style="width:13px;height:13px"></i>
                Sign Out
            </button>
        </form>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <span class="page-label">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            <div class="topbar-status">
                <div class="pulse"></div>
                System Online
            </div>
            <div class="topbar-divider"></div>
            <div class="topbar-clock" id="topbar-clock"></div>
        </div>
    </header>

    <div class="content">
        @if(session('success'))
        <div class="flash flash-success">
            <i data-lucide="check-circle" style="width:14px;height:14px"></i>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flash flash-error">
            <i data-lucide="x-circle" style="width:14px;height:14px"></i>
            {{ session('error') }}
        </div>
        @endif
        @if($errors->any())
        <div class="flash flash-error">
            <i data-lucide="alert-circle" style="width:14px;height:14px"></i>
            <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        </div>
        @endif
        @yield('content')
    </div>
</div>

<script>
lucide.createIcons();

function tick() {
    const now = new Date();
    const d = now.toLocaleDateString('en-PH', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
    const t = now.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const el = document.getElementById('topbar-clock');
    if (el) el.textContent = d + ' · ' + t;
}
tick();
setInterval(tick, 1000);

function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-backdrop').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});
function confirmDelete(formId) {
    if (confirm('Are you sure? This cannot be undone.')) {
        document.getElementById(formId).submit();
    }
}
</script>
@stack('scripts')
</body>
</html>