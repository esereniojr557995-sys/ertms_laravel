@extends('layouts.app')
@section('title','Citizen Dashboard')
@section('page-title','Community Safety Dashboard')
@section('sidebar-nav')@include('citizen._nav')@endsection

@section('content')

{{-- Emergency banner if critical alerts exist --}}
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
                <div style="flex:1">
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
            <h2><i data-lucide="map-pin"></i> Open Evacuation Shelters</h2>
            <a href="{{ route('citizen.mapping') }}" class="btn btn-secondary btn-sm">Map →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Shelter</th><th>Capacity</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($openShelters as $s)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:.83rem;color:var(--text-bright)">{{ $s->name }}</div>
                        <div style="font-size:.72rem;color:var(--text-muted);margin-top:1px">{{ $s->location }}</div>
                        @if($s->contact_person)
                        <div style="font-size:.7rem;color:var(--text-muted)">{{ $s->contact_person }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="progress-bar" style="margin-bottom:3px">
                            <div class="fill {{ $s->getOccupancyPercent()>=90?'red':($s->getOccupancyPercent()>=60?'yellow':'green') }}"
                                 style="width:{{ $s->getOccupancyPercent() }}%"></div>
                        </div>
                        <div style="font-size:.7rem;color:var(--text-muted)">
                            {{ $s->current_occupancy }}/{{ $s->capacity }}
                            <span style="color:var(--green);margin-left:4px">{{ $s->capacity - $s->current_occupancy }} left</span>
                        </div>
                    </td>
                    <td><span class="badge badge-completed">OPEN</span></td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state"><p>No open shelters available.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
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
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Title</th><th>Type</th><th>Location</th><th>Status</th><th>Submitted</th></tr>
            </thead>
            <tbody>
            @foreach($myReports as $report)
            <tr>
                <td>
                    <div style="font-weight:600;color:var(--text-bright)">{{ $report->title }}</div>
                </td>
                <td><span class="badge badge-info" style="text-transform:capitalize">{{ $report->type }}</span></td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $report->location }}</td>
                <td>
                    <span class="badge badge-{{ $report->status==='acknowledged'?'in_progress':($report->status==='resolved'?'completed':($report->status==='dismissed'?'cancelled':'pending')) }}">
                        {{ str_replace('_',' ',$report->status) }}
                    </span>
                </td>
                <td style="font-size:.75rem;color:var(--text-muted);font-family:var(--font-mono)">
                    {{ $report->created_at->diffForHumans() }}
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection