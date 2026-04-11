@extends('layouts.app')
@section('title','Settings')
@section('page-title','System Settings')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}"><i data-lucide="bell"></i> Alerts</a>
<a href="{{ route('admin.comms') }}" class="{{ request()->routeIs('admin.comms*') ? 'active' : '' }}">
    <i data-lucide="message-square"></i> Communications
</a>
<a href="{{ route('admin.patients') }}"><i data-lucide="heart-pulse"></i> Medical</a>
<a href="{{ route('admin.training') }}"><i data-lucide="graduation-cap"></i> Training</a>
<a href="{{ route('admin.shelters') }}"><i data-lucide="map-pin"></i> Shelters</a>
<div class="sb-section">Management</div>
<a href="{{ route('admin.users') }}"><i data-lucide="users"></i> Users</a>
<a href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-2"></i> Reports</a>
<a href="{{ route('admin.audit_logs') }}"><i data-lucide="scroll-text"></i> Audit Logs</a>
<a href="{{ route('admin.settings') }}" class="active"><i data-lucide="settings"></i> Settings</a>
@endsection

@push('styles')
<style>
    .settings-layout {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 20px;
        align-items: start;
    }
    .settings-tabs {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r);
        overflow: hidden;
        position: sticky;
        top: 0;
    }
    .settings-tabs-header {
        padding: 11px 14px;
        font-size: .6rem;
        font-weight: 700;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: .14em;
        border-bottom: 1px solid var(--border);
        background: var(--surface2);
    }
    .settings-tab {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        font-size: .82rem;
        font-weight: 500;
        color: var(--text-muted);
        cursor: pointer;
        border: none;
        border-right: 2px solid transparent;
        background: none;
        width: 100%;
        text-align: left;
        border-bottom: 1px solid var(--border);
        transition: background .1s, color .1s;
        font-family: var(--font);
        letter-spacing: -.01em;
    }
    .settings-tab:last-child { border-bottom: none; }
    .settings-tab svg { width: 13px; height: 13px; flex-shrink: 0; }
    .settings-tab:hover { background: var(--surface2); color: var(--text); }
    .settings-tab.active { background: var(--accent-dim); color: var(--accent2); border-right: 2px solid var(--accent); }
    .settings-tab.active svg { color: var(--accent); }

    .settings-panel { display: none; flex-direction: column; gap: 16px; }
    .settings-panel.active { display: flex; }

    .settings-section { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r); overflow: hidden; }
    .settings-section-header {
        padding: 13px 18px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: 10px;
        background: var(--surface2);
    }
    .sh-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .sh-icon svg { width: 13px; height: 13px; }
    .sh-icon.blue   { background: var(--blue-dim);   color: var(--blue); }
    .sh-icon.green  { background: var(--green-dim);  color: var(--green); }
    .sh-icon.yellow { background: var(--yellow-dim); color: var(--yellow); }
    .sh-icon.red    { background: var(--red-dim);    color: var(--red); }
    .sh-icon.purple { background: var(--purple-dim); color: var(--purple); }
    .sh-icon.orange { background: var(--accent-dim); color: var(--accent2); }
    .settings-section-header h3 { font-size: .84rem; font-weight: 600; color: var(--text-bright); letter-spacing: -.01em; line-height: 1; }
    .settings-section-header p  { font-size: .7rem; color: var(--text-muted); margin-top: 2px; }

    .info-row { display: flex; align-items: center; justify-content: space-between; padding: 11px 18px; border-bottom: 1px solid rgba(23,32,48,.8); gap: 16px; }
    .info-row:last-child { border-bottom: none; }
    .info-row .ir-label { font-size: .78rem; color: var(--text-muted); font-weight: 500; flex-shrink: 0; }
    .info-row .ir-value { font-size: .8rem; color: var(--text-bright); font-family: var(--font-mono); text-align: right; }
    .info-row .ir-value.normal { font-family: var(--font); }

    .stat-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 18px; border-bottom: 1px solid rgba(23,32,48,.8); gap: 16px; }
    .stat-row:last-child { border-bottom: none; }
    .stat-row-left { display: flex; align-items: center; gap: 10px; }
    .sr-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
    .sr-label { font-size: .8rem; color: var(--text); font-weight: 500; }
    .stat-row-right { display: flex; align-items: center; gap: 10px; }
    .sr-count { font-size: .88rem; font-weight: 600; font-family: var(--font-mono); color: var(--text-bright); min-width: 36px; text-align: right; }
    .sr-bar { width: 80px; height: 4px; background: var(--surface3); border-radius: 3px; overflow: hidden; }
    .sr-bar .fill { height: 100%; border-radius: 3px; }

    .health-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: var(--border); }
    .health-item { background: var(--surface); padding: 14px 18px; }
    .hi-label { font-size: .66rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .09em; margin-bottom: 4px; }
    .hi-value { font-size: .88rem; font-weight: 600; color: var(--text-bright); font-family: var(--font-mono); margin-bottom: 3px; }
    .hi-status { font-size: .7rem; font-weight: 600; }
    .hi-status.ok { color: var(--green); }
    .hi-status.warn { color: var(--yellow); }

    .profile-card { display: flex; align-items: center; gap: 14px; padding: 18px; border-bottom: 1px solid var(--border); }
    .profile-avatar {
        width: 48px; height: 48px; border-radius: 50%;
        background: linear-gradient(135deg, var(--accent), #c93a18);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; font-weight: 700; color: #fff; flex-shrink: 0;
        box-shadow: 0 0 0 2px rgba(240,73,35,.25), 0 4px 12px rgba(240,73,35,.2);
    }
    .pname  { font-size: .95rem; font-weight: 600; color: var(--text-bright); letter-spacing: -.02em; }
    .pemail { font-size: .76rem; color: var(--text-muted); margin-top: 2px; }
    .pbadge { margin-top: 6px; }
    .profile-actions { padding: 13px 18px; display: flex; gap: 8px; }

    .status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: .72rem; font-weight: 600; padding: 3px 10px;
        border-radius: 20px; background: var(--green-dim); color: var(--green);
        border: 1px solid rgba(30,201,109,.2);
    }
    .status-pill .dot { width: 5px; height: 5px; border-radius: 50%; background: var(--green); box-shadow: 0 0 5px var(--green); animation: pulse-dot 2s infinite; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>System Settings</h1>
        <div class="bc">Admin / Settings</div>
    </div>
    <div class="status-pill"><div class="dot"></div> System Online</div>
</div>

<div class="settings-layout">

    <div class="settings-tabs">
        <div class="settings-tabs-header">Navigate</div>
        <button class="settings-tab active" onclick="switchTab(this,'system')">
            <i data-lucide="server"></i> System
        </button>
        <button class="settings-tab" onclick="switchTab(this,'database')">
            <i data-lucide="database"></i> Database
        </button>
        <button class="settings-tab" onclick="switchTab(this,'account')">
            <i data-lucide="user-circle"></i> My Account
        </button>
        <button class="settings-tab" onclick="switchTab(this,'security')">
            <i data-lucide="shield"></i> Security
        </button>
    </div>

    <div>

        {{-- SYSTEM --}}
        <div class="settings-panel active" id="panel-system">

            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="sh-icon blue"><i data-lucide="info"></i></div>
                    <div><h3>Application Info</h3><p>Environment and runtime configuration</p></div>
                </div>
                @foreach($sysInfo as [$label, $value, $mono])
                <div class="info-row">
                    <span class="ir-label">{{ $label }}</span>
                    <span class="ir-value {{ $mono ? '' : 'normal' }}">{{ $value }}</span>
                </div>
                @endforeach
            </div>

            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="sh-icon green"><i data-lucide="activity"></i></div>
                    <div><h3>System Health</h3><p>Service and runtime status at a glance</p></div>
                </div>
                <div class="health-grid">
                    @foreach($healthItems as $item)
                    <div class="health-item">
                        <div class="hi-label">{{ $item['label'] }}</div>
                        <div class="hi-value">{{ $item['value'] }}</div>
                        <div class="hi-status ok">● {{ $item['status'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- DATABASE --}}
        <div class="settings-panel" id="panel-database">

            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="sh-icon purple"><i data-lucide="database"></i></div>
                    <div><h3>Record Counts</h3><p>Total rows across all database tables</p></div>
                </div>
                @php $maxVal = max(array_column($dbStats, 1)) ?: 1; @endphp
                @foreach($dbStats as [$label, $value, $color])
                <div class="stat-row">
                    <div class="stat-row-left">
                        <div class="sr-dot" style="background:{{ $color }};box-shadow:0 0 5px {{ $color }}55"></div>
                        <span class="sr-label">{{ $label }}</span>
                    </div>
                    <div class="stat-row-right">
                        <div class="sr-bar">
                            <div class="fill" style="width:{{ min(100, ($value / $maxVal) * 100) }}%;background:{{ $color }}"></div>
                        </div>
                        <span class="sr-count">{{ number_format($value) }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="sh-icon blue"><i data-lucide="server"></i></div>
                    <div><h3>Connection Details</h3><p>Active database connection configuration</p></div>
                </div>
                @foreach($dbConnection as [$label, $value])
                <div class="info-row">
                    <span class="ir-label">{{ $label }}</span>
                    <span class="ir-value">{{ $value }}</span>
                </div>
                @endforeach
            </div>

        </div>

        {{-- ACCOUNT --}}
        <div class="settings-panel" id="panel-account">
            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="sh-icon orange"><i data-lucide="user-circle"></i></div>
                    <div><h3>My Profile</h3><p>Your account information and details</p></div>
                </div>
                <div class="profile-card">
                    <div class="profile-avatar">{{ $initials }}</div>
                    <div>
                        <div class="pname">{{ auth()->user()->name }}</div>
                        <div class="pemail">{{ auth()->user()->email }}</div>
                        <div class="pbadge"><span class="badge badge-{{ auth()->user()->role }}">{{ auth()->user()->getRoleLabel() }}</span></div>
                    </div>
                </div>
                @foreach($accountInfo as [$label, $value])
                <div class="info-row">
                    <span class="ir-label">{{ $label }}</span>
                    <span class="ir-value normal">{{ $value }}</span>
                </div>
                @endforeach
                <div class="profile-actions">
                    <a href="{{ route('admin.users.edit', auth()->user()) }}" class="btn btn-primary">
                        <i data-lucide="pencil" style="width:13px;height:13px"></i> Edit Profile
                    </a>
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                        <i data-lucide="users" style="width:13px;height:13px"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>

        {{-- SECURITY --}}
        <div class="settings-panel" id="panel-security">

            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="sh-icon red"><i data-lucide="shield"></i></div>
                    <div><h3>Security Configuration</h3><p>Authentication and access control settings</p></div>
                </div>
                @foreach($securityInfo as [$label, $value])
                <div class="info-row">
                    <span class="ir-label">{{ $label }}</span>
                    <span class="ir-value">{{ $value }}</span>
                </div>
                @endforeach
            </div>

            <div class="settings-section">
                <div class="settings-section-header">
                    <div class="sh-icon yellow"><i data-lucide="users"></i></div>
                    <div><h3>Role Distribution</h3><p>Registered users grouped by access level</p></div>
                </div>
                @foreach($roleCounts as $role => $count)
                <div class="info-row">
                    <span class="ir-label"><span class="badge badge-{{ $role }}">{{ $role }}</span></span>
                    <span class="ir-value normal">{{ $count }} {{ Str::plural('user', $count) }}</span>
                </div>
                @endforeach
            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(btn, tab) {
    document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + tab).classList.add('active');
    lucide.createIcons();
}
</script>
@endpush