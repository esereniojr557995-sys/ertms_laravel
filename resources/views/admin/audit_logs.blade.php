{{-- resources/views/admin/audit_logs.blade.php --}}
@extends('layouts.app')
@section('title','Audit Logs')
@section('page-title','Audit Logs')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}"><i data-lucide="bell"></i> Alerts</a>
<a href="{{ route('admin.patients') }}"><i data-lucide="heart-pulse"></i> Medical</a>
<a href="{{ route('admin.training') }}"><i data-lucide="graduation-cap"></i> Training</a>
<a href="{{ route('admin.shelters') }}"><i data-lucide="map-pin"></i> Shelters</a>
<div class="sb-section">Management</div>
<a href="{{ route('admin.users') }}"><i data-lucide="users"></i> Users</a>
<a href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-2"></i> Reports</a>
<a href="{{ route('admin.audit_logs') }}" class="active"><i data-lucide="scroll-text"></i> Audit Logs</a>
<a href="{{ route('admin.settings') }}"><i data-lucide="settings"></i> Settings</a>
@endsection

@section('content')
<div class="page-header">
    <div><h1>Audit Logs</h1><div class="breadcrumb">Admin / Audit Logs</div></div>
</div>
<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="margin:0">
            <select name="module" class="form-control">
                <option value="">All Modules</option>
                @foreach(['Users','Incidents','Resources','Alerts','Medical','Training'] as $m)
                <option value="{{ $m }}" {{ request('module')==$m?'selected':'' }}>{{ $m }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit"><i data-lucide="filter" style="width:13px;height:13px"></i> Filter</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>User</th><th>Action</th><th>Module</th><th>Record ID</th><th>Details</th><th>IP</th><th>Time</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
            <tr>
                <td style="font-weight:500;font-size:.82rem">{{ $log->user?->name ?? 'System' }}</td>
                <td><span class="badge badge-{{ strtolower($log->action)==='create'?'completed':(strtolower($log->action)==='delete'?'active':'in_progress') }}">{{ $log->action }}</span></td>
                <td><span class="badge badge-info">{{ $log->module }}</span></td>
                <td style="font-size:.75rem;color:var(--text-muted)">{{ $log->record_id ?? '—' }}</td>
                <td style="font-size:.75rem;color:var(--text-muted);max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $log->details }}</td>
                <td style="font-size:.72rem;color:var(--text-muted);font-family:monospace">{{ $log->ip_address }}</td>
                <td style="font-size:.72rem;color:var(--text-muted)">{{ $log->created_at->format('M d, H:i:s') }}</td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i data-lucide="scroll-text"></i><p>No activity logs.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $logs->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>
@endsection
