@extends('layouts.app')
@section('title','Users')
@section('page-title','User Management')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.citizen_reports') }}" class="{{ request()->routeIs('admin.citizen_reports*') ? 'active' : '' }}">
    <i data-lucide="file-text"></i> Citizen Reports
</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}"><i data-lucide="bell"></i> Alerts</a>
<a href="{{ route('admin.comms') }}" class="{{ request()->routeIs('admin.comms*') ? 'active' : '' }}">
    <i data-lucide="message-square"></i> Communications
</a>
<a href="{{ route('admin.patients') }}"><i data-lucide="heart-pulse"></i> Medical</a>
<a href="{{ route('admin.training') }}"><i data-lucide="graduation-cap"></i> Training</a>
<a href="{{ route('admin.shelters') }}"><i data-lucide="map-pin"></i> Shelters</a>
<div class="sb-section">Management</div>
<a href="{{ route('admin.users') }}" class="active"><i data-lucide="users"></i> Users</a>
<a href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-2"></i> Reports</a>
<a href="{{ route('admin.audit_logs') }}"><i data-lucide="scroll-text"></i> Audit Logs</a>
<a href="{{ route('admin.settings') }}"><i data-lucide="settings"></i> Settings</a>
@endsection

@section('content')
<div class="page-header">
    <div><h1>Users</h1><div class="breadcrumb">Admin / User Management</div></div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i data-lucide="plus" style="width:14px;height:14px"></i> Add User</a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="margin:0">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search name or email…" style="width:220px">
            <select name="role" class="form-control">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
                <option value="commander" {{ request('role')=='commander'?'selected':'' }}>Commander</option>
                <option value="responder" {{ request('role')=='responder'?'selected':'' }}>Responder</option>
                <option value="citizen" {{ request('role')=='citizen'?'selected':'' }}>Citizen</option>
            </select>
            <button class="btn btn-secondary" type="submit"><i data-lucide="search" style="width:13px;height:13px"></i> Filter</button>
        </form>
        <span style="color:var(--text-muted);font-size:.78rem">{{ $users->total() }} users</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Unit / Rank</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($users as $user)
            <tr>
                <td><div style="font-weight:600">{{ $user->name }}</div><div style="font-size:.7rem;color:var(--text-muted)">{{ $user->phone }}</div></td>
                <td style="color:var(--text-muted);font-size:.8rem">{{ $user->email }}</td>
                <td><span class="badge badge-{{ $user->role }}">{{ $user->getRoleLabel() }}</span></td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $user->unit ?? '—' }}{{ $user->rank ? ' · '.$user->rank : '' }}</td>
                <td><span class="badge badge-{{ in_array($user->status,['active','on_duty']) ? 'completed' : 'cancelled' }}">{{ $user->status }}</span></td>
                <td style="font-size:.75rem;color:var(--text-muted)">{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-xs"><i data-lucide="pencil" style="width:12px;height:12px"></i></a>
                        @if($user->id !== auth()->id())
                        <form id="del-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}">@csrf @method('DELETE')</form>
                        <button class="btn btn-danger btn-xs" onclick="confirmDelete('del-user-{{ $user->id }}')"><i data-lucide="trash-2" style="width:12px;height:12px"></i></button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i data-lucide="users"></i><p>No users found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $users->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>
@endsection
