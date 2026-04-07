@extends('layouts.app')
@section('title','Citizen Dashboard')
@section('page-title','Community Safety Dashboard')
@section('sidebar-nav')@include('citizen._nav')@endsection

@section('content')
{{-- Emergency banner if critical alerts exist --}}
@php $critical = $latestAlerts->where('severity','critical')->first(); @endphp
@if($critical)
<div style="background:rgba(248,81,73,.12);border:1px solid rgba(248,81,73,.4);border-radius:8px;padding:14px 18px;margin-bottom:24px;display:flex;align-items:center;gap:12px">
    <i data-lucide="alert-triangle" style="width:20px;height:20px;color:var(--red);flex-shrink:0"></i>
    <div>
        <div style="font-weight:700;color:var(--red);font-size:.9rem">CRITICAL ALERT: {{ $critical->title }}</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px">{{ Str::limit($critical->message, 120) }}</div>
    </div>
</div>
@endif

<div class="stat-grid">
    <div class="stat-card red">
        <div class="label">Active Incidents</div>
        <div class="value" style="color:var(--red)">{{ $activeIncidents }}</div>
        <div class="sub">in your area</div>
    </div>
    <div class="stat-card yellow">
        <div class="label">Active Alerts</div>
        <div class="value" style="color:var(--yellow)">{{ $latestAlerts->count() }}</div>
        <div class="sub">advisories issued</div>
    </div>
    <div class="stat-card green">
        <div class="label">Open Shelters</div>
        <div class="value" style="color:var(--green)">{{ $openShelters->count() }}</div>
        <div class="sub">accepting evacuees</div>
    </div>
    <div class="stat-card blue">
        <div class="label">My Reports</div>
        <div class="value" style="color:var(--blue)">{{ $myReports->count() }}</div>
        <div class="sub">submitted by you</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    {{-- Latest Alerts --}}
    <div class="card">
        <div class="card-header">
            <h2><i data-lucide="bell" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Latest Alerts</h2>
            <a href="{{ route('citizen.alerts') }}" class="btn btn-secondary btn-sm">All →</a>
        </div>
        <div class="card-body">
            @forelse($latestAlerts as $alert)
            <div class="alert-row">
                <div class="alert-dot {{ $alert->severity }}"></div>
                <div style="flex:1">
                    <div class="alert-title">{{ $alert->title }}</div>
                    <div class="alert-meta">{{ ucfirst($alert->type) }} · {{ $alert->created_at->diffForHumans() }}</div>
                    <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px">{{ Str::limit($alert->message, 100) }}</div>
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
            <h2><i data-lucide="map-pin" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Open Evacuation Shelters</h2>
            <a href="{{ route('citizen.mapping') }}" class="btn btn-secondary btn-sm">Map →</a>
        </div>
        <div class="card-body" style="padding:0">
            @forelse($openShelters as $s)
            <div style="padding:12px 16px;border-bottom:1px solid rgba(48,54,61,.5)">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <div style="font-weight:600;font-size:.83rem">{{ $s->name }}</div>
                        <div style="font-size:.72rem;color:var(--text-muted)"><i data-lucide="map-pin" style="width:10px;height:10px;display:inline"></i> {{ $s->location }}</div>
                        @if($s->contact_person)<div style="font-size:.72rem;color:var(--text-muted)"><i data-lucide="phone" style="width:10px;height:10px;display:inline"></i> {{ $s->contact_person }}</div>@endif
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <span class="badge badge-completed">OPEN</span>
                        <div style="font-size:.7rem;color:var(--text-muted);margin-top:4px">{{ $s->capacity - $s->current_occupancy }} slots left</div>
                    </div>
                </div>
                <div style="margin-top:6px">
                    <div class="progress-bar">
                        <div class="fill {{ $s->getOccupancyPercent()>=90?'red':($s->getOccupancyPercent()>=60?'yellow':'green') }}" style="width:{{ $s->getOccupancyPercent() }}%"></div>
                    </div>
                    <div style="font-size:.68rem;color:var(--text-muted);margin-top:2px">{{ $s->current_occupancy }}/{{ $s->capacity }} occupants</div>
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
        <h2><i data-lucide="file-text" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>My Recent Reports</h2>
        <a href="{{ route('citizen.portal') }}" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:13px;height:13px"></i> New Report</a>
    </div>
    @if($myReports->isEmpty())
    <div class="empty-state" style="padding:32px">
        <i data-lucide="file-plus"></i>
        <p>You haven't submitted any reports yet.</p>
        <a href="{{ route('citizen.portal') }}" class="btn btn-primary" style="margin-top:12px"><i data-lucide="plus" style="width:14px;height:14px"></i> Submit a Report</a>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead><tr><th>Title</th><th>Type</th><th>Location</th><th>Status</th><th>Submitted</th></tr></thead>
            <tbody>
            @foreach($myReports as $report)
            <tr>
                <td style="font-weight:600;font-size:.83rem">{{ $report->title }}</td>
                <td><span class="badge badge-info" style="text-transform:capitalize">{{ $report->type }}</span></td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $report->location }}</td>
                <td>
                    <span class="badge badge-{{ $report->status==='acknowledged'?'in_progress':($report->status==='resolved'?'completed':($report->status==='dismissed'?'cancelled':'pending')) }}">
                        {{ $report->status }}
                    </span>
                </td>
                <td style="font-size:.75rem;color:var(--text-muted)">{{ $report->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
