@extends('layouts.app')
@section('title','Edit User')
@section('page-title','Edit User')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.citizen_reports') }}" class="{{ request()->routeIs('admin.citizen_reports*') ? 'active' : '' }}">
    <i data-lucide="scroll-text"></i> Citizen Reports
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
    <div><h1>Edit: {{ $user->name }}</h1><div class="breadcrumb">Admin / Users / Edit</div></div>
    <a href="{{ route('admin.users') }}" class="btn btn-secondary"><i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back</a>
</div>

<div class="card" style="max-width:680px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group"><label>Full Name *</label><input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required></div>
                <div class="form-group"><label>Email Address *</label><input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>New Password <span style="color:var(--text-muted)">(leave blank to keep)</span></label><input type="password" name="password" class="form-control"></div>
                <div class="form-group"><label>Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" class="form-control" required>
                        <option value="admin" {{ old('role',$user->role)=='admin'?'selected':'' }}>System Administrator</option>
                        <option value="commander" {{ old('role',$user->role)=='commander'?'selected':'' }}>Incident Commander</option>
                        <option value="responder" {{ old('role',$user->role)=='responder'?'selected':'' }}>Field Responder</option>
                        <option value="citizen" {{ old('role',$user->role)=='citizen'?'selected':'' }}>Citizen</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control" required>
                        @foreach(['active','inactive','on_duty','off_duty','unavailable'] as $s)
                        <option value="{{ $s }}" {{ old('status',$user->status)==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Phone</label><input type="text" name="phone" value="{{ old('phone',$user->phone) }}" class="form-control"></div>
                <div class="form-group"><label>Unit / Team</label><input type="text" name="unit" value="{{ old('unit',$user->unit) }}" class="form-control"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Rank</label><input type="text" name="rank" value="{{ old('rank',$user->rank) }}" class="form-control"></div>
                <div class="form-group"><label>Specialization</label><input type="text" name="specialization" value="{{ old('specialization',$user->specialization) }}" class="form-control"></div>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px"></i> Update User</button>
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
