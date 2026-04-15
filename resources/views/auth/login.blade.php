<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERTMS — Access</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        :root{--bg:#080c10;--surface:#0e1420;--surface2:#141c2a;--border:#1e2d42;--text:#d4dde8;--muted:#5a7090;--accent:#ff4d1c;--accent2:#ff7a45;--green:#22d07a;--red:#ff4d4d;}
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:stretch;}

        /* LEFT PANEL */
        .auth-left{flex:1;display:flex;align-items:center;justify-content:center;padding:60px 48px;position:relative;overflow:hidden;}
        .auth-left::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 25% 50%,rgba(255,77,28,.08) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(59,158,255,.04) 0%,transparent 50%);}
        .grid-bg{position:absolute;inset:0;background-image:linear-gradient(rgba(30,45,66,.3) 1px,transparent 1px),linear-gradient(90deg,rgba(30,45,66,.3) 1px,transparent 1px);background-size:40px 40px;mask-image:radial-gradient(ellipse at center,black 0%,transparent 70%);}
        .brand-block{position:relative;max-width:400px;}
        .shield-wrap{width:72px;height:72px;background:var(--accent);border-radius:18px;display:flex;align-items:center;justify-content:center;box-shadow:0 0 32px rgba(255,77,28,.4),0 8px 24px rgba(255,77,28,.2);margin-bottom:28px;}
        .brand-block h1{font-family:'Syne',sans-serif;font-size:3.2rem;font-weight:800;color:#fff;letter-spacing:-.04em;line-height:1;}
        .brand-block .tagline{font-size:.82rem;color:var(--muted);margin-top:10px;text-transform:uppercase;letter-spacing:.12em;font-weight:500;}
        .brand-block .desc{margin-top:28px;color:var(--muted);font-size:.84rem;line-height:1.7;border-left:2px solid rgba(255,77,28,.3);padding-left:14px;}
        .features{margin-top:32px;display:grid;grid-template-columns:1fr 1fr;gap:10px;}
        .feat{display:flex;align-items:center;gap:8px;font-size:.76rem;color:var(--muted);background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:6px;padding:8px 10px;}
        .feat i{width:13px;height:13px;color:var(--accent);flex-shrink:0;}

        /* RIGHT PANEL */
        .auth-right{width:420px;background:var(--surface);border-left:1px solid var(--border);display:flex;align-items:center;justify-content:center;padding:40px 36px;overflow-y:auto;}
        .auth-box{width:100%;}

        /* TAB SWITCHER */
        .tab-switcher{display:flex;background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:9px;padding:4px;margin-bottom:28px;gap:4px;}
        .tab-btn{flex:1;padding:8px 12px;border:none;border-radius:6px;font-size:.78rem;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .15s;background:transparent;color:var(--muted);}
        .tab-btn.active{background:var(--accent);color:#fff;box-shadow:0 2px 8px rgba(255,77,28,.3);}
        .tab-btn:not(.active):hover{color:var(--text);background:rgba(255,255,255,.04);}

        /* PANEL VISIBILITY */
        .tab-panel{display:none;}
        .tab-panel.active{display:block;}

        .form-title{font-family:'Syne',sans-serif;font-size:1.25rem;font-weight:700;color:#fff;margin-bottom:3px;}
        .form-sub{font-size:.76rem;color:var(--muted);margin-bottom:22px;line-height:1.5;}

        /* FORM ELEMENTS */
        .form-group{margin-bottom:13px;}
        .form-group label{display:block;font-size:.65rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.09em;margin-bottom:5px;}
        .input-wrap{position:relative;}
        .input-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--muted);pointer-events:none;}
        .form-control{width:100%;padding:9px 11px 9px 34px;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:7px;color:var(--text);font-size:.82rem;font-family:'DM Sans',sans-serif;transition:border-color .15s,box-shadow .15s;}
        .form-control:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(255,77,28,.1);background:rgba(255,77,28,.025);}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;}

        .check-row{display:flex;align-items:center;gap:7px;font-size:.76rem;color:var(--muted);margin-bottom:18px;}
        .check-row input{accent-color:var(--accent);width:13px;height:13px;}

        .btn-submit{width:100%;padding:11px;border-radius:7px;background:var(--accent);border:none;color:#fff;font-size:.87rem;font-weight:700;font-family:'Syne',sans-serif;cursor:pointer;letter-spacing:.02em;box-shadow:0 4px 16px rgba(255,77,28,.3);transition:all .15s;display:flex;align-items:center;justify-content:center;gap:7px;}
        .btn-submit:hover{background:#ff3a00;transform:translateY(-1px);box-shadow:0 6px 20px rgba(255,77,28,.4);}
        .btn-submit i{width:15px;height:15px;}

        /* ERROR / SUCCESS BOXES */
        .error-box{background:rgba(255,77,77,.08);border:1px solid rgba(255,77,77,.2);border-radius:6px;padding:9px 12px;font-size:.76rem;color:var(--red);margin-bottom:14px;display:flex;align-items:flex-start;gap:7px;}
        .error-box i{width:14px;height:14px;flex-shrink:0;margin-top:1px;}
        .success-box{background:rgba(34,208,122,.06);border:1px solid rgba(34,208,122,.2);border-radius:6px;padding:9px 12px;font-size:.76rem;color:var(--green);margin-bottom:14px;display:flex;align-items:center;gap:7px;}
        .success-box i{width:14px;height:14px;flex-shrink:0;}

        /* CITIZEN NOTICE */
        .citizen-notice{background:rgba(59,158,255,.06);border:1px solid rgba(59,158,255,.18);border-radius:6px;padding:10px 12px;font-size:.74rem;color:#7bbdff;margin-bottom:16px;display:flex;align-items:flex-start;gap:8px;line-height:1.5;}
        .citizen-notice i{width:13px;height:13px;flex-shrink:0;margin-top:1px;}

        /* DIVIDER */
        .divider{display:flex;align-items:center;gap:10px;margin:16px 0;color:var(--muted);font-size:.7rem;}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border);}

        /* DEMO PANEL */
        .demo-panel{margin-top:20px;padding:13px;background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:8px;}
        .demo-panel .dp-title{font-size:.6rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:9px;}
        .demo-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid rgba(30,45,66,.6);}
        .demo-row:last-child{border-bottom:none;}
        .demo-row .role{font-size:.72rem;font-weight:600;color:var(--text);}
        .demo-row .email{font-size:.68rem;color:var(--muted);font-family:monospace;}

        /* PASSWORD STRENGTH */
        .pw-strength{margin-top:5px;height:3px;border-radius:2px;background:var(--border);overflow:hidden;}
        .pw-strength .bar{height:100%;border-radius:2px;width:0%;transition:width .3s,background .3s;}
        .pw-hint{font-size:.65rem;color:var(--muted);margin-top:4px;}

        @media(max-width:768px){.auth-left{display:none;}.auth-right{width:100%;}}
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="grid-bg"></div>
        <div class="brand-block">
            <div class="shield-wrap">
                <i data-lucide="shield-alert" style="width:34px;height:34px;color:#fff"></i>
            </div>
            <h1>ERTMS</h1>
            <div class="tagline">Emergency Response Team Management</div>
            <p class="desc">A unified command platform for emergency operations, multi-team coordination, and citizen safety.</p>
            <div class="features">
                <div class="feat"><i data-lucide="flame"></i>Incident Command</div>
                <div class="feat"><i data-lucide="users"></i>Team Coordination</div>
                <div class="feat"><i data-lucide="package"></i>Resource Control</div>
                <div class="feat"><i data-lucide="map"></i>Live Mapping</div>
                <div class="feat"><i data-lucide="heart-pulse"></i>Medical Triage</div>
                <div class="feat"><i data-lucide="file-plus"></i>Citizen Reports</div>
            </div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-box">

            {{-- Tab Switcher --}}
            <div class="tab-switcher">
                <button class="tab-btn {{ !session('show_register') ? 'active' : '' }}" onclick="switchTab('login')">
                    Sign In
                </button>
                <button class="tab-btn {{ session('show_register') ? 'active' : '' }}" onclick="switchTab('register')">
                    Create Account
                </button>
            </div>

            {{-- ── SIGN IN PANEL ──────────────────────────────────── --}}
            <div class="tab-panel {{ !session('show_register') ? 'active' : '' }}" id="panel-login">
                <div class="form-title">Welcome back</div>
                <div class="form-sub">Sign in to the emergency response platform</div>

                @if(session('success'))
                <div class="success-box"><i data-lucide="check-circle"></i>{{ session('success') }}</div>
                @endif

                @if($errors->loginBag->any())
                <div class="error-box"><i data-lucide="alert-circle"></i>{{ $errors->loginBag->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="input-wrap">
                            <i data-lucide="mail"></i>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="you@ertms.gov" required autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrap">
                            <i data-lucide="lock"></i>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="check-row">
                        <input type="checkbox" name="remember" id="rem">
                        <label for="rem">Keep me signed in</label>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i data-lucide="log-in"></i> Sign In to ERTMS
                    </button>
                </form>
            </div>

            {{-- ── REGISTER PANEL ─────────────────────────────────── --}}
            <div class="tab-panel {{ session('show_register') ? 'active' : '' }}" id="panel-register">
                <div class="form-title">Create Account</div>
                <div class="form-sub">Register as a citizen to report incidents and access emergency info</div>

                <div class="citizen-notice">
                    <i data-lucide="info"></i>
                    Public registration creates a <strong style="color:#fff">Citizen</strong> account. Responder and Commander accounts are created by the System Administrator.
                </div>

                @if($errors->registerBag->any())
                <div class="error-box">
                    <i data-lucide="alert-circle"></i>
                    <div>@foreach($errors->registerBag->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name *</label>
                            <div class="input-wrap">
                                <i data-lucide="user"></i>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control" placeholder="Juan" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Last Name *</label>
                            <div class="input-wrap">
                                <i data-lucide="user"></i>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control" placeholder="Dela Cruz" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <div class="input-wrap">
                            <i data-lucide="mail"></i>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="your@email.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <div class="input-wrap">
                            <i data-lucide="phone"></i>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="09XXXXXXXXX">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <div class="input-wrap">
                            <i data-lucide="lock"></i>
                            <input type="password" name="password" id="reg-password" class="form-control" placeholder="Min. 8 characters" required oninput="checkStrength(this.value)">
                        </div>
                        <div class="pw-strength"><div class="bar" id="pw-bar"></div></div>
                        <div class="pw-hint" id="pw-hint">Enter a password</div>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <div class="input-wrap">
                            <i data-lucide="lock-keyhole"></i>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>
                    <div class="check-row" style="margin-bottom:16px">
                        <input type="checkbox" id="agree" required>
                        <label for="agree">I agree to use this platform responsibly for emergency purposes</label>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i data-lucide="user-plus"></i> Create Citizen Account
                    </button>
                </form>

                <div class="divider">Already have an account?</div>
                <button class="btn-submit" onclick="switchTab('login')" style="background:transparent;border:1px solid var(--border);color:var(--muted);box-shadow:none;">
                    <i data-lucide="log-in"></i> Sign In Instead
                </button>
            </div>

        </div>
    </div>

    <script>
    lucide.createIcons();

    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.getElementById('panel-' + tab).classList.add('active');
        event.currentTarget.classList.add('active');
        // Sync both buttons when called from link
        const btns = document.querySelectorAll('.tab-btn');
        btns[0].classList.toggle('active', tab === 'login');
        btns[1].classList.toggle('active', tab === 'register');
    }

    function checkStrength(val) {
        const bar = document.getElementById('pw-bar');
        const hint = document.getElementById('pw-hint');
        let score = 0;
        if (val.length >= 8)  score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = [
            { w:'0%',   bg:'transparent', txt:'Enter a password' },
            { w:'25%',  bg:'#ff4d4d',     txt:'Weak' },
            { w:'50%',  bg:'#f5b731',     txt:'Fair' },
            { w:'75%',  bg:'#3b9eff',     txt:'Good' },
            { w:'100%', bg:'#22d07a',     txt:'Strong ✓' },
        ];
        bar.style.width = levels[score].w;
        bar.style.background = levels[score].bg;
        hint.textContent = levels[score].txt;
        hint.style.color = levels[score].bg || 'var(--muted)';
    }

    // Auto-switch to register tab if there were register errors
    @if($errors->registerBag->any() || session('show_register'))
    switchTab('register');
    @endif
    </script>
</body>
</html>
