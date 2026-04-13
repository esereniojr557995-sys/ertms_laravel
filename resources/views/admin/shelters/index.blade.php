@extends('layouts.app')
@section('title','Shelters')
@section('page-title','Shelter Management')

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
<a href="{{ route('admin.shelters') }}" class="active"><i data-lucide="map-pin"></i> Shelters</a>
<div class="sb-section">Management</div>
<a href="{{ route('admin.users') }}"><i data-lucide="users"></i> Users</a>
<a href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-2"></i> Reports</a>
<a href="{{ route('admin.audit_logs') }}"><i data-lucide="scroll-text"></i> Audit Logs</a>
<a href="{{ route('admin.settings') }}"><i data-lucide="settings"></i> Settings</a>
@endsection

@section('content')
<div class="page-header">
    <div><h1>Evacuation Shelters</h1><div class="breadcrumb">Admin / Shelters</div></div>
    <button class="btn btn-primary" onclick="openModal('modal-add-shelter')">
        <i data-lucide="plus" style="width:14px;height:14px"></i> Add Shelter
    </button>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Shelter</th><th>Location</th><th>Occupancy</th><th>Contact</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($shelters as $s)
            <tr>
                <td style="font-weight:600">{{ $s->name }}</td>
                <td style="font-size:.78rem;color:var(--text-muted)"><i data-lucide="map-pin" style="width:11px;height:11px;display:inline"></i> {{ $s->location }}</td>
                <td style="min-width:140px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="flex:1">
                            <div class="progress-bar">
                                <div class="fill {{ $s->getOccupancyPercent() >= 90 ? 'red' : ($s->getOccupancyPercent() >= 60 ? 'yellow' : 'green') }}" style="width:{{ $s->getOccupancyPercent() }}%"></div>
                            </div>
                        </div>
                        <span style="font-size:.72rem;color:var(--text-muted);white-space:nowrap">{{ $s->current_occupancy }}/{{ $s->capacity }}</span>
                    </div>
                </td>
                <td style="font-size:.78rem">{{ $s->contact_person ?? '—' }}<br><span style="color:var(--text-muted)">{{ $s->contact_no }}</span></td>
                <td>
                    <span class="badge badge-{{ $s->status === 'open' ? 'completed' : ($s->status === 'full' ? 'high' : 'cancelled') }}">
                        {{ $s->status }}
                    </span>
                </td>
                <td>
                    <button class="btn btn-secondary btn-xs" onclick="openModal('edit-shelter-{{ $s->id }}')">
                        <i data-lucide="pencil" style="width:12px;height:12px"></i> Update
                    </button>
                </td>
            </tr>
            {{-- Edit Shelter Modal --}}
            <div class="modal-backdrop" id="edit-shelter-{{ $s->id }}">
                <div class="modal">
                    <div class="modal-header"><h3>Update: {{ $s->name }}</h3><button class="modal-close" onclick="closeModal('edit-shelter-{{ $s->id }}')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.shelters.update', $s) }}">
                            @csrf @method('PUT')
                            <div class="form-row">
                                <div class="form-group"><label>Current Occupancy *</label><input type="number" name="current_occupancy" value="{{ $s->current_occupancy }}" class="form-control" min="0" max="{{ $s->capacity }}" required></div>
                                <div class="form-group">
                                    <label>Status *</label>
                                    <select name="status" class="form-control">
                                        <option value="open" {{ $s->status=='open'?'selected':'' }}>Open</option>
                                        <option value="full" {{ $s->status=='full'?'selected':'' }}>Full</option>
                                        <option value="closed" {{ $s->status=='closed'?'selected':'' }}>Closed</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px"></i> Save</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i data-lucide="map-pin"></i><p>No shelters found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $shelters->links('vendor.pagination.custom') }}</div>
</div>

{{-- Add Shelter Modal --}}
<div class="modal-backdrop" id="modal-add-shelter">
    <div class="modal">
        <div class="modal-header"><h3>Add Evacuation Shelter</h3><button class="modal-close" onclick="closeModal('modal-add-shelter')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.shelters.store') }}">
                @csrf
                <div class="form-group"><label>Shelter Name *</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Address / Location *</label><input type="text" name="location" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Latitude</label><input type="number" step="0.0000001" name="latitude" class="form-control"></div>
                    <div class="form-group"><label>Longitude</label><input type="number" step="0.0000001" name="longitude" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Total Capacity *</label><input type="number" name="capacity" class="form-control" min="0" required></div>
                    <div class="form-group"><label>Current Occupancy *</label><input type="number" name="current_occupancy" value="0" class="form-control" min="0" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Contact Person</label><input type="text" name="contact_person" class="form-control"></div>
                    <div class="form-group"><label>Contact No.</label><input type="text" name="contact_no" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control">
                        <option value="open">Open</option><option value="full">Full</option><option value="closed">Closed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i data-lucide="plus" style="width:14px;height:14px"></i> Add Shelter</button>
            </form>
        </div>
    </div>
</div>
@endsection
