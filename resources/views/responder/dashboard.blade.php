@extends('layouts.app')
@section('title','Responder Dashboard')
@section('page-title','Field Operations Dashboard')
@section('sidebar-nav')@include('responder._nav')@endsection

@section('content')
<div class="stat-grid">
    <div class="stat-card yellow">
        <div class="label">My Pending Tasks</div>
        <div class="value" style="color:var(--yellow)">{{ $myTasks }}</div>
        <div class="sub">awaiting completion</div>
    </div>
    <div class="stat-card red">
        <div class="label">Active Alerts</div>
        <div class="value" style="color:var(--red)">{{ $activeAlerts->count() }}</div>
        <div class="sub">in effect now</div>
    </div>
    <div class="stat-card blue">
        <div class="label">Active Incidents</div>
        <div class="value" style="color:var(--blue)">{{ $recentIncidents->count() }}</div>
        <div class="sub">open or active</div>
    </div>
    <div class="stat-card green">
        <div class="label">My Trainings</div>
        <div class="value" style="color:var(--green)">{{ $myTraining->count() }}</div>
        <div class="sub">enrolled / completed</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header">
            <h2><i data-lucide="bell" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Active Alerts</h2>
            <a href="{{ route('responder.alerts') }}" class="btn btn-secondary btn-sm">All →</a>
        </div>
        <div class="card-body">
            @forelse($activeAlerts as $alert)
            <div class="alert-row">
                <div class="alert-dot {{ $alert->severity }}"></div>
                <div>
                    <div class="alert-title">{{ $alert->title }}</div>
                    <div class="alert-meta">{{ $alert->type }} · {{ $alert->created_at->diffForHumans() }}</div>
                </div>
                <span class="badge badge-{{ $alert->severity }}" style="flex-shrink:0">{{ $alert->severity }}</span>
            </div>
            @empty
            <div class="empty-state" style="padding:16px"><p>No active alerts.</p></div>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i data-lucide="flame" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Active Incidents</h2>
            <a href="{{ route('responder.incidents') }}" class="btn btn-secondary btn-sm">All →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Incident</th><th>Severity</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($recentIncidents as $inc)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div class="inc-icon {{ $inc->type }}" style="width:26px;height:26px">
                                <i data-lucide="{{ $inc->getTypeIcon() }}" style="width:12px;height:12px"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.82rem">{{ $inc->title }}</div>
                                <div style="font-size:.7rem;color:var(--text-muted)">{{ $inc->location }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-{{ $inc->severity }}">{{ $inc->severity }}</span></td>
                    <td><span class="badge badge-{{ $inc->status }}">{{ $inc->status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state" style="padding:16px"><p>No active incidents.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i data-lucide="graduation-cap" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>My Training Records</h2>
        <a href="{{ route('responder.training') }}" class="btn btn-secondary btn-sm">All Training →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Program</th><th>Type</th><th>Scheduled</th><th>Status</th><th>Score</th></tr></thead>
            <tbody>
            @forelse($myTraining as $record)
            <tr>
                <td style="font-weight:600;font-size:.83rem">{{ $record->program->title }}</td>
                <td><span class="badge badge-info">{{ $record->program->type }}</span></td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $record->program->date_scheduled?->format('M d, Y') ?? '—' }}</td>
                <td><span class="badge badge-{{ $record->status==='passed'?'completed':($record->status==='enrolled'?'in_progress':($record->status==='failed'?'active':'cancelled')) }}">{{ $record->status }}</span></td>
                <td style="font-size:.82rem">{{ $record->score ? $record->score.'%' : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state" style="padding:20px"><p>No training records. <a href="{{ route('responder.training') }}" style="color:var(--accent)">Browse programs →</a></p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
