@extends('layouts.app')
@section('title','Citizen Dashboard')
@section('page-title','Community Safety Dashboard')
@section('sidebar-nav')@include('citizen._nav')@endsection

@section('content')

@php $critical = $latestAlerts->where('severity','critical')->first(); @endphp
@if($critical)
<div class="warning-strip" style="margin-bottom:20px">
    <i data-lucide="alert-triangle"></i>
    <div>
        <div style="font-weight:700;font-size:.84rem">CRITICAL ALERT: {{ $critical->title }}</div>
        <div style="font-size:.78rem;margin-top:1px;color:var(--accent3)">{{ Str::limit($critical->message, 120) }}</div>
    </div>
</div>
@endif

{{-- Stat Cards --}}
<div class="stat-grid" style="margin-bottom:20px">
    <div class="stat-card red">
        <div class="sc-icon"><i data-lucide="flame"></i></div>
        <div class="sc-val">{{ $activeIncidents }}</div>
        <div class="sc-label">Active Incidents</div>
        <div class="sc-sub">in your area</div>
    </div>
    <div class="stat-card yellow">
        <div class="sc-icon"><i data-lucide="bell"></i></div>
        <div class="sc-val">{{ $latestAlerts->count() }}</div>
        <div class="sc-label">Active Alerts</div>
        <div class="sc-sub">advisories issued</div>
    </div>
    <div class="stat-card green">
        <div class="sc-icon"><i data-lucide="home"></i></div>
        <div class="sc-val">{{ $openShelters->count() }}</div>
        <div class="sc-label">Open Shelters</div>
        <div class="sc-sub">accepting evacuees</div>
    </div>
    <div class="stat-card blue">
        <div class="sc-icon"><i data-lucide="file-text"></i></div>
        <div class="sc-val">{{ $myReports->count() }}</div>
        <div class="sc-label">My Reports</div>
        <div class="sc-sub">submitted by you</div>
    </div>
</div>

{{-- Quick Actions (mobile-first) --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px">
    <a href="{{ route('citizen.alerts') }}" class="btn btn-secondary" style="justify-content:center;padding:12px">
        <i data-lucide="bell" style="width:15px;height:15px"></i> View Alerts
    </a>
    <a href="{{ route('citizen.portal') }}" class="btn btn-primary" style="justify-content:center;padding:12px">
        <i data-lucide="plus" style="width:15px;height:15px"></i> Report Incident
    </a>
    <a href="{{ route('citizen.mapping') }}" class="btn btn-secondary" style="justify-content:center;padding:12px">
        <i data-lucide="map" style="width:15px;height:15px"></i> Evacuation Map
    </a>
    <a href="{{ route('citizen.portal') }}" class="btn btn-secondary" style="justify-content:center;padding:12px">
        <i data-lucide="clipboard-list" style="width:15px;height:15px"></i> My Reports
    </a>
</div>

<div class="grid-2" style="margin-bottom:16px">

    {{-- Latest Alerts --}}
    <div class="card">
        <div class="card-header">
            <h2><i data-lucide="bell"></i> Latest Alerts</h2>
            <a href="{{ route('citizen.alerts') }}" class="btn btn-secondary btn-sm">All →</a>
        </div>
        <div class="card-body">
            @forelse($latestAlerts as $alert)
            <div class="alert-row">
                <div class="alert-dot {{ $alert->severity }}"></div>
                <div style="flex:1;min-width:0">
                    <div class="alert-title">{{ $alert->title }}</div>
                    <div class="alert-meta">{{ ucfirst($alert->type) }} · {{ $alert->created_at->diffForHumans() }}</div>
                    <div style="font-size:.75rem;color:var(--text-muted);margin-top:3px">{{ Str::limit($alert->message, 100) }}</div>
                </div>
                <span class="badge badge-{{ $alert->severity }}" style="flex-shrink:0">{{ $alert->severity }}</span>
            </div>
            @empty
            <div class="empty-state" style="padding:20px"><p>No active alerts.</p></div>
            @endforelse
        </div>
    </div>

    {{-- Open Shelters --}}
    <div class="card">
        <div class="card-header">
            <h2><i data-lucide="map-pin"></i> Open Shelters</h2>
            <a href="{{ route('citizen.mapping') }}" class="btn btn-secondary btn-sm">Map →</a>
        </div>
        <div class="card-body" style="padding:0">
            @forelse($openShelters as $s)
            <div style="padding:12px 16px;border-bottom:1px solid rgba(23,32,48,.8)">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                    <div style="min-width:0">
                        <div style="font-weight:600;font-size:.83rem;color:var(--text-bright)">{{ $s->name }}</div>
                        <div style="font-size:.72rem;color:var(--text-muted);margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $s->location }}</div>
                    </div>
                    <span class="badge badge-completed" style="flex-shrink:0">OPEN</span>
                </div>
                <div style="margin-top:7px">
                    <div class="progress-bar" style="margin-bottom:3px">
                        <div class="fill {{ $s->getOccupancyPercent()>=90?'red':($s->getOccupancyPercent()>=60?'yellow':'green') }}"
                             style="width:{{ $s->getOccupancyPercent() }}%"></div>
                    </div>
                    <div style="font-size:.7rem;color:var(--text-muted)">
                        {{ $s->current_occupancy }}/{{ $s->capacity }}
                        <span style="color:var(--green);margin-left:4px">{{ $s->capacity - $s->current_occupancy }} slots left</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:20px"><p>No open shelters available.</p></div>
            @endforelse
        </div>
    </div>

</div>

{{-- My Recent Reports --}}
<div class="card">
    <div class="card-header">
        <h2><i data-lucide="file-text"></i> My Recent Reports</h2>
        <a href="{{ route('citizen.portal') }}" class="btn btn-primary btn-sm">
            <i data-lucide="plus" style="width:13px;height:13px"></i> New Report
        </a>
    </div>
    @if($myReports->isEmpty())
    <div class="empty-state" style="padding:40px">
        <i data-lucide="file-plus"></i>
        <p>You haven't submitted any reports yet.</p>
        <a href="{{ route('citizen.portal') }}" class="btn btn-primary" style="margin-top:12px">
            <i data-lucide="plus" style="width:14px;height:14px"></i> Submit a Report
        </a>
    </div>
    @else
    {{-- Card list on mobile, table on desktop --}}
    <div class="table-wrap" style="display:none" id="reports-table">
        <table>
            <thead><tr><th>Title</th><th>Type</th><th>Location</th><th>Status</th><th>Submitted</th></tr></thead>
            <tbody>
            @foreach($myReports as $report)
            <tr>
                <td><div style="font-weight:600;color:var(--text-bright)">{{ $report->title }}</div></td>
                <td><span class="badge badge-info" style="text-transform:capitalize">{{ $report->type }}</span></td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $report->location }}</td>
                <td>
                    <span class="badge badge-{{ $report->status==='acknowledged'?'in_progress':($report->status==='resolved'?'completed':($report->status==='dismissed'?'cancelled':'pending')) }}">
                        {{ str_replace('_',' ',$report->status) }}
                    </span>
                </td>
                <td style="font-size:.75rem;color:var(--text-muted);font-family:var(--font-mono)">{{ $report->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{-- Mobile card list --}}
    <div id="reports-cards">
        @foreach($myReports as $report)
        <div style="padding:13px 16px;border-bottom:1px solid rgba(23,32,48,.8)">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                <div style="min-width:0">
                    <div style="font-weight:600;font-size:.84rem;color:var(--text-bright)">{{ $report->title }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px">
                        <i data-lucide="map-pin" style="width:10px;height:10px;display:inline;vertical-align:middle"></i>
                        {{ $report->location }}
                    </div>
                </div>
                <a href="{{ route('citizen.portal.show', $report) }}" class="btn btn-secondary btn-xs" style="flex-shrink:0">
                    <i data-lucide="eye" style="width:11px;height:11px"></i>
                </a>
            </div>
            <div style="display:flex;gap:6px;margin-top:8px;flex-wrap:wrap;align-items:center">
                <span class="badge badge-info" style="text-transform:capitalize">{{ $report->type }}</span>
                <span class="badge badge-{{ $report->status==='acknowledged'?'in_progress':($report->status==='resolved'?'completed':($report->status==='dismissed'?'cancelled':'pending')) }}">
                    {{ str_replace('_',' ',$report->status) }}
                </span>
                <span style="font-size:.7rem;color:var(--text-muted);margin-left:auto">{{ $report->created_at->diffForHumans() }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection