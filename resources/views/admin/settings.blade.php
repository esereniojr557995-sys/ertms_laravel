@extends('layouts.app')
@section('title','Settings')
@section('page-title','System Settings')

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
<a href="{{ route('admin.audit_logs') }}"><i data-lucide="scroll-text"></i> Audit Logs</a>
<a href="{{ route('admin.settings') }}" class="active"><i data-lucide="settings"></i> Settings</a>
@endsection

@section('content')
<div class="page-header">
    <div><h1>System Settings</h1><div class="breadcrumb">Admin / Settings</div></div>
</div>

<div style="max-width:600px;display:flex;flex-direction:column;gap:20px">
    <div class="card">
        <div class="card-header"><h2><i data-lucide="info" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>System Information</h2></div>
        <div class="card-body">
            @foreach([
                ['System Name','Emergency Response Team Management System'],
                ['Version','1.0.0'],
                ['Framework','Laravel 10'],
                ['PHP Version', PHP_VERSION],
                ['Environment', config('app.env')],
                ['Timezone', config('app.timezone')],
            ] as [$label,$value])
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(48,54,61,.5)">
                <span style="color:var(--text-muted);font-size:.82rem">{{ $label }}</span>
                <span style="font-size:.82rem;font-family:monospace">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2><i data-lucide="database" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Database Statistics</h2></div>
        <div class="card-body">
            @foreach([
                ['Users', \App\Models\User::count()],
                ['Incidents', \App\Models\Incident::count()],
                ['Tasks', \App\Models\Task::count()],
                ['Resources', \App\Models\Resource::count()],
                ['Alerts Sent', \App\Models\Alert::count()],
                ['Patient Records', \App\Models\Patient::count()],
                ['Citizen Reports', \App\Models\CitizenReport::count()],
                ['Audit Log Entries', \App\Models\AuditLog::count()],
            ] as [$label,$value])
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(48,54,61,.5)">
                <span style="color:var(--text-muted);font-size:.82rem">{{ $label }}</span>
                <span style="font-size:.82rem;font-weight:600;color:var(--accent)">{{ number_format($value) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2><i data-lucide="user" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>My Account</h2></div>
        <div class="card-body">
            <div style="margin-bottom:16px">
                <div style="font-weight:600;font-size:1rem">{{ auth()->user()->name }}</div>
                <div style="font-size:.8rem;color:var(--text-muted)">{{ auth()->user()->email }}</div>
                <div style="margin-top:6px"><span class="badge badge-{{ auth()->user()->role }}">{{ auth()->user()->getRoleLabel() }}</span></div>
            </div>
            <a href="{{ route('admin.users.edit', auth()->user()) }}" class="btn btn-secondary">
                <i data-lucide="pencil" style="width:14px;height:14px"></i> Edit Profile
            </a>
        </div>
    </div>
</div>
@endsection
