<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERTMS — Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700&family=Geist+Mono:wght@400;500&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        :root {
            --bg: #05080d;
            --surface: #080e18;
            --surface2: #0c1522;
            --border: #172030;
            --border2: #1e2d42;
            --text: #c8d8e8;
            --text-bright: #e8f0f8;
            --muted: #4a6480;
            --dim: #2d4257;
            --accent: #f04923;
            --accent2: #f5724a;
            --green: #1ec96d;
            --red: #e84545;
            --font: 'Geist', system-ui, sans-serif;
            --font-mono: 'Geist Mono', monospace;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { -webkit-font-smoothing: antialiased; }
        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        /* ── LEFT PANEL ───────────────────────────────────────── */
        .login-left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 64px;
            position: relative;
            overflow: hidden;
        }

        /* Grid background */
        .grid-bg {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(23,32,48,.5) 1px, transparent 1px),
                linear-gradient(90deg, rgba(23,32,48,.5) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(ellipse 80% 80% at 30% 50%, black 0%, transparent 70%);
        }

        /* Glow orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }
        .orb-1 {
            width: 400px; height: 300px;
            background: rgba(240,73,35,.07);
            top: 10%; left: -10%;
        }
        .orb-2 {
            width: 300px; height: 300px;
            background: rgba(58,142,255,.04);
            bottom: 15%; right: 10%;
        }

        .brand-block {
            position: relative;
            max-width: 420px;
            z-index: 1;
        }

        /* Logo mark */
        .logo-mark {
            display: flex;
            align-items: center;
            gap: 13px;
            margin-bottom: 40px;
        }
        .logo-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, var(--accent), #c93a18);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                0 0 0 1px rgba(240,73,35,.35),
                0 8px 24px rgba(240,73,35,.2),
                0 2px 6px rgba(0,0,0,.3);
        }
        .logo-text .name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-bright);
            letter-spacing: .04em;
            line-height: 1;
        }
        .logo-text .full {
            font-size: .65rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .14em;
            font-weight: 500;
            margin-top: 4px;
        }

        .brand-heading {
            font-family: 'Instrument Serif', Georgia, serif;
            font-size: 2.8rem;
            color: var(--text-bright);
            line-height: 1.12;
            letter-spacing: -.02em;
            margin-bottom: 14px;
        }
        .brand-heading em {
            font-style: italic;
            color: var(--accent2);
        }

        .brand-desc {
            font-size: .83rem;
            color: var(--muted);
            line-height: 1.75;
            max-width: 340px;
        }

        .divider {
            width: 40px;
            height: 1px;
            background: var(--border2);
            margin: 24px 0;
        }

        .features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .feat {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: .74rem;
            color: var(--muted);
            background: rgba(255,255,255,.018);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 7px 10px;
            transition: border-color .15s, color .15s;
        }
        .feat:hover {
            border-color: var(--border2);
            color: var(--text);
        }
        .feat svg { width: 12px; height: 12px; color: var(--accent); flex-shrink: 0; }

        /* ── RIGHT PANEL ──────────────────────────────────────── */
        .login-right {
            width: 420px;
            background: var(--surface);
            border-left: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
        }
        .login-form { width: 100%; }

        .form-eyebrow {
            font-size: .62rem;
            font-weight: 600;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: .14em;
            margin-bottom: 8px;
        }
        .form-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-bright);
            letter-spacing: -.03em;
            margin-bottom: 5px;
        }
        .form-sub {
            font-size: .77rem;
            color: var(--muted);
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .form-group { margin-bottom: 14px; }
        .form-group label {
            display: block;
            font-size: .63rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 6px;
        }
        .input-wrap { position: relative; }
        .input-wrap svg {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px; height: 14px;
            color: var(--dim);
            pointer-events: none;
        }
        .form-control {
            width: 100%;
            padding: 9.5px 11px 9.5px 34px;
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: 6px;
            color: var(--text-bright);
            font-size: .82rem;
            font-family: var(--font);
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .form-control::placeholder { color: var(--dim); }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(240,73,35,.08);
            background: rgba(240,73,35,.02);
        }

        .check-row {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: .77rem;
            color: var(--muted);
            margin-bottom: 20px;
            cursor: pointer;
        }
        .check-row input {
            accent-color: var(--accent);
            width: 13px; height: 13px;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            background: var(--accent);
            border: none;
            color: #fff;
            font-size: .84rem;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            letter-spacing: -.01em;
            box-shadow:
                0 0 0 1px rgba(240,73,35,.4),
                0 4px 12px rgba(240,73,35,.2);
            transition: all .15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-login svg { width: 14px; height: 14px; }
        .btn-login:hover {
            background: #d93e1c;
            transform: translateY(-1px);
            box-shadow: 0 0 0 1px rgba(240,73,35,.35), 0 8px 20px rgba(240,73,35,.28);
        }
        .btn-login:active { transform: translateY(0); }

        .error-box {
            background: rgba(232,69,69,.07);
            border: 1px solid rgba(232,69,69,.18);
            border-radius: 6px;
            padding: 9px 11px;
            font-size: .77rem;
            color: var(--red);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .error-box svg { width: 13px; height: 13px; flex-shrink: 0; }

        /* Demo panel */
        .demo-panel {
            margin-top: 22px;
            padding: 13px;
            background: rgba(255,255,255,.015);
            border: 1px solid var(--border);
            border-radius: 8px;
        }
        .demo-panel .dp-title {
            font-size: .6rem;
            font-weight: 600;
            color: var(--dim);
            text-transform: uppercase;
            letter-spacing: .12em;
            margin-bottom: 10px;
        }
        .demo-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid rgba(23,32,48,.7);
        }
        .demo-row:last-child { border-bottom: none; }
        .demo-row .role {
            font-size: .73rem;
            font-weight: 600;
            color: var(--text);
        }
        .demo-row .email {
            font-size: .68rem;
            color: var(--muted);
            font-family: var(--font-mono);
        }

        @media(max-width: 768px) {
            .login-left { display: none; }
            .login-right { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="login-left">
        <div class="grid-bg"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>

        <div class="brand-block">
            <div class="logo-mark">
                <div class="logo-icon">
                    <i data-lucide="shield-alert" style="width:22px;height:22px;color:#fff"></i>
                </div>
                <div class="logo-text">
                    <div class="name">ERTMS</div>
                    <div class="full">Emergency Response</div>
                </div>
            </div>

            <h1 class="brand-heading">
                Unified command<br>
                for <em>critical response</em>
            </h1>

            <p class="brand-desc">
                A centralized platform for incident command, multi-team coordination, live situational awareness, and citizen safety operations.
            </p>

            <div class="divider"></div>

            <div class="features">
                <div class="feat">
                    <i data-lucide="flame"></i>
                    Incident Command
                </div>
                <div class="feat">
                    <i data-lucide="users"></i>
                    Team Coordination
                </div>
                <div class="feat">
                    <i data-lucide="package"></i>
                    Resource Control
                </div>
                <div class="feat">
                    <i data-lucide="map"></i>
                    Live Mapping
                </div>
                <div class="feat">
                    <i data-lucide="heart-pulse"></i>
                    Medical Triage
                </div>
                <div class="feat">
                    <i data-lucide="file-plus"></i>
                    Citizen Reports
                </div>
            </div>
        </div>
    </div>

    <div class="login-right">
        <div class="login-form">
            <div class="form-eyebrow">Secure Access</div>
            <div class="form-title">Welcome back</div>
            <div class="form-sub">Sign in to the emergency response platform</div>

            @if($errors->any())
            <div class="error-box">
                <i data-lucide="alert-circle"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrap">
                        <i data-lucide="mail"></i>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control"
                            placeholder="you@ertms.gov"
                            required autofocus
                        >
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <i data-lucide="lock"></i>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                </div>
                <label class="check-row">
                    <input type="checkbox" name="remember" id="rem">
                    Keep me signed in
                </label>
                <button type="submit" class="btn-login">
                    <i data-lucide="log-in"></i>
                    Sign In to ERTMS
                </button>
            </form>

            <div class="demo-panel">
                <div class="dp-title">Demo Accounts — password: password</div>
                <div class="demo-row">
                    <span class="role">Admin</span>
                    <span class="email">admin@ertms.gov</span>
                </div>
                <div class="demo-row">
                    <span class="role">Commander</span>
                    <span class="email">commander@ertms.gov</span>
                </div>
                <div class="demo-row">
                    <span class="role">Responder</span>
                    <span class="email">responder@ertms.gov</span>
                </div>
                <div class="demo-row">
                    <span class="role">Citizen</span>
                    <span class="email">citizen@ertms.gov</span>
                </div>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>