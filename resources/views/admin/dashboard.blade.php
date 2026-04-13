@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Operations Dashboard')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i data-lucide="layout-dashboard"></i> Dashboard
</a>
<a href="{{ route('admin.incidents') }}" class="{{ request()->routeIs('admin.incidents*') ? 'active' : '' }}">
    <i data-lucide="flame"></i> Incidents
    @if($stats['active_incidents'] > 0)<span class="nb">{{ $stats['active_incidents'] }}</span>@endif
</a>
<a href="{{ route('admin.citizen_reports') }}" class="{{ request()->routeIs('admin.citizen_reports*') ? 'active' : '' }}">
    <i data-lucide="file-text"></i> Citizen Reports
</a>
<a href="{{ route('admin.resources') }}" class="{{ request()->routeIs('admin.resources*') ? 'active' : '' }}">
    <i data-lucide="package"></i> Resources
    @if($stats['low_resources'] > 0)<span class="nb">{{ $stats['low_resources'] }}</span>@endif
</a>
<a href="{{ route('admin.alerts') }}" class="{{ request()->routeIs('admin.alerts*') ? 'active' : '' }}">
    <i data-lucide="bell"></i> Alerts
</a>

<a href="{{ route('admin.comms') }}" class="{{ request()->routeIs('admin.comms*') ? 'active' : '' }}">
    <i data-lucide="message-square"></i> Communications
</a>

<a href="{{ route('admin.patients') }}" class="{{ request()->routeIs('admin.patients*') ? 'active' : '' }}">
    <i data-lucide="heart-pulse"></i> Medical
</a>
<a href="{{ route('admin.training') }}" class="{{ request()->routeIs('admin.training*') ? 'active' : '' }}">
    <i data-lucide="graduation-cap"></i> Training
</a>
<a href="{{ route('admin.shelters') }}" class="{{ request()->routeIs('admin.shelters*') ? 'active' : '' }}">
    <i data-lucide="map-pin"></i> Shelters
</a>
<div class="sb-section">Management</div>
<a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
    <i data-lucide="users"></i> Users
</a>
<a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
    <i data-lucide="bar-chart-2"></i> Reports
</a>
<a href="{{ route('admin.audit_logs') }}" class="{{ request()->routeIs('admin.audit_logs') ? 'active' : '' }}">
    <i data-lucide="scroll-text"></i> Audit Logs
</a>
<a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
    <i data-lucide="settings"></i> Settings
</a>
@endsection

@section('content')

<div class="stat-grid">
    <div class="stat-card red">
        <div class="sc-icon"><i data-lucide="flame"></i></div>
        <div class="sc-val">{{ $stats['active_incidents'] }}</div>
        <div class="sc-label">Active Incidents</div>
        <div class="sc-sub">{{ $stats['critical_incidents'] }} critical</div>
    </div>
    <div class="stat-card blue">
        <div class="sc-icon"><i data-lucide="users"></i></div>
        <div class="sc-val">{{ $stats['active_responders'] }}</div>
        <div class="sc-label">Active Responders</div>
        <div class="sc-sub">on duty now</div>
    </div>
    <div class="stat-card green">
        <div class="sc-icon"><i data-lucide="user-check"></i></div>
        <div class="sc-val">{{ $stats['total_users'] }}</div>
        <div class="sc-label">Total Users</div>
        <div class="sc-sub">registered accounts</div>
    </div>
    <div class="stat-card yellow">
        <div class="sc-icon"><i data-lucide="clipboard-list"></i></div>
        <div class="sc-val">{{ $stats['pending_tasks'] }}</div>
        <div class="sc-label">Pending Tasks</div>
        <div class="sc-sub">awaiting action</div>
    </div>
    <div class="stat-card orange">
        <div class="sc-icon"><i data-lucide="heart-pulse"></i></div>
        <div class="sc-val">{{ $stats['patients_today'] }}</div>
        <div class="sc-label">Patients Today</div>
        <div class="sc-sub">intake this shift</div>
    </div>
    <div class="stat-card purple">
        <div class="sc-icon"><i data-lucide="home"></i></div>
        <div class="sc-val">{{ $stats['open_shelters'] }}</div>
        <div class="sc-label">Open Shelters</div>
        <div class="sc-sub">accepting evacuees</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:16px">
    <div class="card">
        <div class="card-header">
            <h2><i data-lucide="flame"></i> Recent Incidents</h2>
            <a href="{{ route('admin.incidents') }}" class="btn btn-secondary btn-sm">View All →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th>Incident</th><th>Severity</th><th>Status</th><th>Reported</th>
                </tr></thead>
                <tbody>
                @forelse($recentIncidents as $inc)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="inc-icon {{ $inc->type }}">
                                <i data-lucide="{{ $inc->getTypeIcon() }}"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.8rem;color:var(--text-bright)">{{ $inc->title }}</div>
                                <div style="font-size:.68rem;color:var(--text-muted);margin-top:1px">{{ $inc->location }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-{{ $inc->severity }}">{{ $inc->severity }}</span></td>
                    <td><span class="badge badge-{{ $inc->status }}">{{ $inc->status }}</span></td>
                    <td style="color:var(--text-muted);font-size:.72rem;font-family:var(--font-mono)">{{ $inc->date_reported->format('M d, H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="4"><div class="empty-state"><p>No incidents recorded.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i data-lucide="bell"></i> Recent Alerts</h2>
            <div style="display:flex; gap:8px;">
                {{-- Quick Link to Comms added here --}}
                <a href="{{ route('admin.comms') }}" class="btn btn-secondary btn-sm"><i data-lucide="message-square" style="width:14px; height:14px;"></i></a>
                <a href="{{ route('admin.alerts') }}" class="btn btn-secondary btn-sm">All →</a>
            </div>
        </div>
        <div class="card-body">
            @forelse($recentAlerts as $alert)
            <div class="alert-row">
                <div class="alert-dot {{ $alert->severity }}"></div>
                <div>
                    <div class="alert-title">{{ $alert->title }}</div>
                    <div class="alert-meta">{{ $alert->type }} · {{ $alert->sender->name }} · {{ $alert->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div class="empty-state"><p>No alerts sent yet.</p></div>
            @endforelse
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i data-lucide="scroll-text"></i> Recent Activity Log</h2>
        <a href="{{ route('admin.audit_logs') }}" class="btn btn-secondary btn-sm">Full Log →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>User</th><th>Action</th><th>Module</th><th>Details</th><th>Time</th>
            </tr></thead>
            <tbody>
            @forelse($recentLogs as $log)
            <tr>
                <td style="font-weight:500;color:var(--text-bright)">{{ $log->user?->name ?? 'System' }}</td>
                <td><span class="badge badge-{{ strtolower($log->action) === 'create' ? 'completed' : (strtolower($log->action) === 'delete' ? 'active' : 'in_progress') }}">{{ $log->action }}</span></td>
                <td style="color:var(--text-muted)">{{ $log->module }}</td>
                <td style="color:var(--text-muted);font-size:.72rem;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $log->details }}</td>
                <td style="color:var(--text-muted);font-size:.72rem;font-family:var(--font-mono);white-space:nowrap">{{ $log->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state"><p>No activity recorded.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection