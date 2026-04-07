@extends('layouts.app')
@section('title','Responder Dashboard')
@section('page-title','Field Operations Dashboard')
@section('sidebar-nav')@include('responder._nav')@endsection

@section('content')
<div class="stat-grid">
    <div class="stat-card yellow">
        <div class="sc-icon"><i data-lucide="clipboard-list"></i></div>
        <div class="sc-val">{{ $myTasks }}</div>
        <div class="sc-label">My Pending Tasks</div>
        <div class="sc-sub">awaiting completion</div>
    </div>
    <div class="stat-card red">
        <div class="sc-icon"><i data-lucide="bell"></i></div>
        <div class="sc-val">{{ $activeAlerts->count() }}</div>
        <div class="sc-label">Active Alerts</div>
        <div class="sc-sub">in effect now</div>
    </div>
    <div class="stat-card blue">
        <div class="sc-icon"><i data-lucide="flame"></i></div>
        <div class="sc-val">{{ $recentIncidents->count() }}</div>
        <div class="sc-label">Active Incidents</div>
        <div class="sc-sub">open or active</div>
    </div>
    <div class="stat-card green">
        <div class="sc-icon"><i data-lucide="graduation-cap"></i></div>
        <div class="sc-val">{{ $myTraining->count() }}</div>
        <div class="sc-label">My Trainings</div>
        <div class="sc-sub">enrolled / completed</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:16px">
    <div class="card">
        <div class="card-header">
            <h2><i data-lucide="bell"></i> Active Alerts</h2>
            <a href="{{ route('responder.alerts') }}" class="btn btn-secondary btn-sm">All →</a>
        </div>
        <div class="card-body">
            @forelse($activeAlerts as $alert)
            <div class="alert-row">
                <div class="alert-dot {{ $alert->severity }}"></div>
                <div style="flex:1">
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
            <h2><i data-lucide="flame"></i> Active Incidents</h2>
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
        <h2><i data-lucide="graduation-cap"></i> My Training Records</h2>
        <a href="{{ route('responder.training') }}" class="btn btn-secondary btn-sm">All Training →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Program</th><th>Type</th><th>Scheduled</th><th>Status</th><th>Score</th></tr></thead>
            <tbody>
            @forelse($myTraining as $record)
            <tr>
                <td style="font-weight:600;font-size:.8rem;color:var(--text-bright)">{{ $record->program->title }}</td>
                <td><span class="badge badge-info">{{ $record->program->type }}</span></td>
                <td style="font-size:.72rem;color:var(--text-muted);font-family:var(--font-mono)">{{ $record->program->date_scheduled?->format('M d, Y') ?? '—' }}</td>
                <td><span class="badge badge-{{ $record->status==='passed'?'completed':($record->status==='enrolled'?'in_progress':($record->status==='failed'?'active':'cancelled')) }}">{{ $record->status }}</span></td>
                <td style="font-size:.8rem;font-family:var(--font-mono)">{{ $record->score ? $record->score.'%' : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state" style="padding:20px"><p>No training records. <a href="{{ route('responder.training') }}" style="color:var(--accent)">Browse programs →</a></p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection