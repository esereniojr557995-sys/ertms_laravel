@extends('layouts.app')
@section('title','Commander Dashboard')
@section('page-title','Operations Dashboard')

@section('sidebar-nav')
<div class="sidebar-section">Operations</div>
<a href="{{ route('commander.dashboard') }}" class="{{ request()->routeIs('commander.dashboard') ? 'active' : '' }}">
    <i data-lucide="layout-dashboard"></i> Dashboard
</a>
<a href="{{ route('commander.incidents') }}" class="{{ request()->routeIs('commander.incidents*') ? 'active' : '' }}">
    <i data-lucide="flame"></i> Incidents
</a>
<a href="{{ route('commander.tasks') }}" class="{{ request()->routeIs('commander.tasks*') ? 'active' : '' }}">
    <i data-lucide="check-square"></i> Tasks
    @if($pendingTasks > 0)<span class="badge">{{ $pendingTasks }}</span>@endif
</a>
<a href="{{ route('commander.resources') }}" class="{{ request()->routeIs('commander.resources*') ? 'active' : '' }}">
    <i data-lucide="package"></i> Resources
</a>
<a href="{{ route('commander.alerts') }}" class="{{ request()->routeIs('commander.alerts*') ? 'active' : '' }}">
    <i data-lucide="bell"></i> Alerts
</a>
<a href="{{ route('commander.comms') }}" class="{{ request()->routeIs('commander.comms*') ? 'active' : '' }}">
    <i data-lucide="message-circle"></i> Communications
</a>
<a href="{{ route('commander.patients') }}" class="{{ request()->routeIs('commander.patients*') ? 'active' : '' }}">
    <i data-lucide="heart-pulse"></i> Medical
</a>
<a href="{{ route('commander.mapping') }}" class="{{ request()->routeIs('commander.mapping') ? 'active' : '' }}">
    <i data-lucide="map"></i> Live Map
</a>
<a href="{{ route('commander.reports') }}" class="{{ request()->routeIs('commander.reports') ? 'active' : '' }}">
    <i data-lucide="bar-chart-2"></i> Reports
</a>
@endsection

@section('content')
<div class="stat-grid">
    <div class="stat-card red">
        <div class="label">My Active Incidents</div>
        <div class="value" style="color:var(--red)">{{ $myIncidents }}</div>
        <div class="sub">under command</div>
    </div>
    <div class="stat-card yellow">
        <div class="label">Pending Tasks</div>
        <div class="value" style="color:var(--yellow)">{{ $pendingTasks }}</div>
        <div class="sub">awaiting action</div>
    </div>
    <div class="stat-card blue">
        <div class="label">Active Team</div>
        <div class="value" style="color:var(--blue)">{{ $activeTeam }}</div>
        <div class="sub">responders on duty</div>
    </div>
    <div class="stat-card orange">
        <div class="label">Open Patients</div>
        <div class="value" style="color:var(--accent)">{{ $openPatients }}</div>
        <div class="sub">on scene / transported</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header">
            <h2><i data-lucide="flame" style="width:15px;height:15px;display:inline;vertical-align:middle;margin-right:6px"></i>Recent Incidents</h2>
            <a href="{{ route('commander.incidents') }}" class="btn btn-secondary btn-sm">View All →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Incident</th><th>Severity</th><th>Status</th><th>Reported</th></tr></thead>
                <tbody>
                @forelse($recentIncidents as $inc)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="inc-icon {{ $inc->type }}"><i data-lucide="{{ $inc->getTypeIcon() }}" style="width:14px;height:14px"></i></div>
                            <div>
                                <div style="font-weight:600;font-size:.82rem">{{ $inc->title }}</div>
                                <div style="font-size:.7rem;color:var(--text-muted)">{{ $inc->location }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-{{ $inc->severity }}">{{ $inc->severity }}</span></td>
                    <td><span class="badge badge-{{ $inc->status }}">{{ $inc->status }}</span></td>
                    <td style="color:var(--text-muted);font-size:.75rem">{{ $inc->date_reported->format('M d, H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="4"><div class="empty-state" style="padding:20px"><p>No incidents.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i data-lucide="check-square" style="width:15px;height:15px;display:inline;vertical-align:middle;margin-right:6px"></i>My Pending Tasks</h2>
            <a href="{{ route('commander.tasks') }}" class="btn btn-secondary btn-sm">All Tasks →</a>
        </div>
        <div class="card-body">
            @forelse($myTasks as $task)
            <div style="padding:10px 0;border-bottom:1px solid rgba(48,54,61,.5);display:flex;align-items:flex-start;gap:10px">
                <div style="width:8px;height:8px;border-radius:50%;margin-top:5px;flex-shrink:0;background:{{ $task->priority==='critical'?'var(--red)':($task->priority==='high'?'var(--accent)':($task->priority==='medium'?'var(--yellow)':'var(--green)')) }}"></div>
                <div style="flex:1">
                    <div style="font-weight:600;font-size:.82rem">{{ $task->title }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted)">{{ $task->incident->title }}</div>
                    @if($task->due_datetime)<div style="font-size:.7rem;color:var(--accent);margin-top:2px"><i data-lucide="clock" style="width:11px;height:11px;display:inline"></i> Due {{ $task->due_datetime->format('M d, H:i') }}</div>@endif
                </div>
                <span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span>
            </div>
            @empty
            <div class="empty-state" style="padding:20px"><p>No pending tasks.</p></div>
            @endforelse
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i data-lucide="bell" style="width:15px;height:15px;display:inline;vertical-align:middle;margin-right:6px"></i>Recent Alerts</h2>
        <a href="{{ route('commander.alerts') }}" class="btn btn-secondary btn-sm">All →</a>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        @forelse($recentAlerts as $alert)
        <div class="alert-row" style="padding:12px;background:var(--surface2);border-radius:6px;border:1px solid var(--border)">
            <div class="alert-dot {{ $alert->severity }}"></div>
            <div>
                <div class="alert-title">{{ $alert->title }}</div>
                <div class="alert-meta">{{ $alert->type }} · {{ $alert->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div class="empty-state" style="padding:16px;grid-column:1/-1"><p>No recent alerts.</p></div>
        @endforelse
        </div>
    </div>
</div>
@endsection
