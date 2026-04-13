@extends('layouts.app')
@section('title','Incidents')
@section('page-title','Incident Management')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}" class="active"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.citizen_reports') }}" class="{{ request()->routeIs('admin.citizen_reports*') ? 'active' : '' }}">
    <i data-lucide="file-text"></i> Citizen Reports
</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}""><i data-lucide="bell"></i> Alerts</a>
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
<a href="{{ route('admin.settings') }}"><i data-lucide="settings"></i> Settings</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Incidents</h1>
        <div class="bc">Admin / Incidents</div>
    </div>
    <div class="status-pill"><div class="dot"></div> System Online</div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="margin:0">
            <select name="status" class="form-control">
                <option value="">All Status</option>
                @foreach(['open','active','contained','closed'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="severity" class="form-control">
                <option value="">All Severity</option>
                @foreach(['low','moderate','high','critical'] as $s)
                <option value="{{ $s }}" {{ request('severity')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit"><i data-lucide="filter" style="width:13px;height:13px"></i> Filter</button>
        </form>
        <span style="color:var(--text-muted);font-size:.78rem">{{ $incidents->total() }} records</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Incident</th><th>Type</th><th>Severity</th><th>Status</th><th>Commander</th><th>Reported</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($incidents as $inc)
            <tr>
                <td>
                    <div style="font-weight:600;font-size:.83rem">{{ $inc->title }}</div>
                    <div style="font-size:.7rem;color:var(--text-muted)"><i data-lucide="map-pin" style="width:11px;height:11px;display:inline"></i> {{ $inc->location }}</div>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:6px">
                        <div class="inc-icon {{ $inc->type }}" style="width:24px;height:24px">
                            <i data-lucide="{{ $inc->getTypeIcon() }}" style="width:12px;height:12px"></i>
                        </div>
                        <span style="font-size:.78rem;text-transform:capitalize">{{ $inc->type }}</span>
                    </div>
                </td>
                <td><span class="badge badge-{{ $inc->severity }}">{{ $inc->severity }}</span></td>
                <td><span class="badge badge-{{ $inc->status }}">{{ $inc->status }}</span></td>
                <td style="font-size:.78rem">{{ $inc->commander?->name ?? '—' }}</td>
                <td style="font-size:.75rem;color:var(--text-muted)">{{ $inc->date_reported->format('M d, H:i') }}</td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="{{ route('admin.incidents.edit', $inc) }}" class="btn btn-secondary btn-xs"><i data-lucide="pencil" style="width:12px;height:12px"></i> Edit</a>
                        <form id="del-inc-{{ $inc->id }}" method="POST" action="{{ route('admin.incidents.destroy', $inc) }}">@csrf @method('DELETE')</form>
                        <button class="btn btn-danger btn-xs" onclick="confirmDelete('del-inc-{{ $inc->id }}')"><i data-lucide="trash-2" style="width:12px;height:12px"></i></button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i data-lucide="flame"></i><p>No incidents found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $incidents->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>
@endsection
