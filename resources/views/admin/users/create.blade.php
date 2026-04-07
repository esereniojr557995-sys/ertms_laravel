{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')
@section('title','Add User')
@section('page-title','Add New User')

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
<a href="{{ route('admin.users') }}" class="active"><i data-lucide="users"></i> Users</a>
<a href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-2"></i> Reports</a>
<a href="{{ route('admin.audit_logs') }}"><i data-lucide="scroll-text"></i> Audit Logs</a>
<a href="{{ route('admin.settings') }}"><i data-lucide="settings"></i> Settings</a>
@endsection

@section('content')
<div class="page-header">
    <div><h1>Add User</h1><div class="breadcrumb">Admin / Users / New</div></div>
    <a href="{{ route('admin.users') }}" class="btn btn-secondary"><i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back</a>
</div>

<div class="card" style="max-width:680px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group"><label>Full Name *</label><input type="text" name="name" value="{{ old('name') }}" class="form-control" required></div>
                <div class="form-group"><label>Email Address *</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Password *</label><input type="password" name="password" class="form-control" required></div>
                <div class="form-group"><label>Confirm Password *</label><input type="password" name="password_confirmation" class="form-control" required></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" class="form-control" required>
                        <option value="">— Select Role —</option>
                        <option value="admin" {{ old('role')=='admin'?'selected':'' }}>System Administrator</option>
                        <option value="commander" {{ old('role')=='commander'?'selected':'' }}>Incident Commander</option>
                        <option value="responder" {{ old('role')=='responder'?'selected':'' }}>Field Responder</option>
                        <option value="citizen" {{ old('role')=='citizen'?'selected':'' }}>Citizen</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control" required>
                        <option value="active" {{ old('status','active')=='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>Inactive</option>
                        <option value="on_duty" {{ old('status')=='on_duty'?'selected':'' }}>On Duty</option>
                        <option value="off_duty" {{ old('status')=='off_duty'?'selected':'' }}>Off Duty</option>
                        <option value="unavailable" {{ old('status')=='unavailable'?'selected':'' }}>Unavailable</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Phone</label><input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="09XXXXXXXXX"></div>
                <div class="form-group"><label>Unit / Team</label><input type="text" name="unit" value="{{ old('unit') }}" class="form-control"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Rank</label><input type="text" name="rank" value="{{ old('rank') }}" class="form-control"></div>
                <div class="form-group"><label>Specialization</label><input type="text" name="specialization" value="{{ old('specialization') }}" class="form-control"></div>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px"></i> Create User</button>
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
