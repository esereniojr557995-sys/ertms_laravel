{{-- resources/views/admin/incidents/create.blade.php --}}
@extends('layouts.app')
@section('title','New Incident')
@section('page-title','Create Incident')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}" class="active"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}"><i data-lucide="bell"></i> Alerts</a>
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
    <div><h1>New Incident</h1><div class="breadcrumb">Admin / Incidents / Create</div></div>
    <a href="{{ route('admin.incidents') }}" class="btn btn-secondary"><i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back</a>
</div>

<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.incidents.store') }}">
            @csrf
            <div class="form-group"><label>Incident Title *</label><input type="text" name="title" value="{{ old('title') }}" class="form-control" required placeholder="Brief description of the incident"></div>
            <div class="form-row">
                <div class="form-group">
                    <label>Incident Type *</label>
                    <select name="type" class="form-control" required>
                        <option value="">— Select Type —</option>
                        @foreach(['fire','flood','earthquake','medical','rescue','hazmat','wind','other'] as $t)
                        <option value="{{ $t }}" {{ old('type')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Severity Level *</label>
                    <select name="severity" class="form-control" required>
                        @foreach(['low','moderate','high','critical'] as $s)
                        <option value="{{ $s }}" {{ old('severity')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Location *</label><input type="text" name="location" value="{{ old('location') }}" class="form-control" required placeholder="Barangay, City"></div>
            <div class="form-row">
                <div class="form-group"><label>Latitude</label><input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" class="form-control" placeholder="7.0731"></div>
                <div class="form-group"><label>Longitude</label><input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" class="form-control" placeholder="125.6128"></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control" required>
                        @foreach(['open','active','contained','closed'] as $s)
                        <option value="{{ $s }}" {{ old('status','open')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Assign Commander</label>
                    <select name="commander_id" class="form-control">
                        <option value="">— Unassigned —</option>
                        @foreach($commanders as $c)
                        <option value="{{ $c->id }}" {{ old('commander_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="4" placeholder="Detailed description…">{{ old('description') }}</textarea></div>
            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px"></i> Create Incident</button>
                <a href="{{ route('admin.incidents') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
