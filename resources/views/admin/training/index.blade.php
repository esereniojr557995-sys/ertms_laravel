@extends('layouts.app')
@section('title','Training')
@section('page-title','Training & Certification Management')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.citizen_reports') }}" class="active"><i data-lucide="file-text"></i> Citizen Reports</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}"><i data-lucide="bell"></i> Alerts</a>
<a href="{{ route('admin.comms') }}" class="{{ request()->routeIs('admin.comms*') ? 'active' : '' }}">
    <i data-lucide="message-square"></i> Communications
</a>
<a href="{{ route('admin.patients') }}"><i data-lucide="heart-pulse"></i> Medical</a>
<a href="{{ route('admin.training') }}" class="active"><i data-lucide="graduation-cap"></i> Training</a>
<a href="{{ route('admin.shelters') }}"><i data-lucide="map-pin"></i> Shelters</a>
<div class="sb-section">Management</div>
<a href="{{ route('admin.users') }}"><i data-lucide="users"></i> Users</a>
<a href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-2"></i> Reports</a>
<a href="{{ route('admin.audit_logs') }}"><i data-lucide="scroll-text"></i> Audit Logs</a>
<a href="{{ route('admin.settings') }}"><i data-lucide="settings"></i> Settings</a>
@endsection

@section('content')
<div class="page-header">
    <div><h1>Training Programs</h1><div class="breadcrumb">Admin / Training</div></div>
    <button class="btn btn-primary" onclick="openModal('modal-add-training')">
        <i data-lucide="plus" style="width:14px;height:14px"></i> Add Program
    </button>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Program</th><th>Type</th><th>Scheduled</th><th>Location</th><th>Trainer</th><th>Capacity</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($programs as $prog)
            <tr>
                <td>
                    <div style="font-weight:600">{{ $prog->title }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted)">{{ Str::limit($prog->description, 60) }}</div>
                </td>
                <td><span class="badge badge-info" style="text-transform:capitalize">{{ $prog->type }}</span></td>
                <td style="font-size:.78rem">{{ $prog->date_scheduled?->format('M d, Y') ?? '—' }}</td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $prog->location ?? '—' }}</td>
                <td style="font-size:.78rem">{{ $prog->trainer?->name ?? '—' }}</td>
                <td style="font-size:.78rem">
                    {{ $prog->records->count() }} / {{ $prog->max_participants }}
                    <div class="progress-bar" style="margin-top:4px">
                        <div class="fill green" style="width:{{ $prog->max_participants > 0 ? round($prog->records->count()/$prog->max_participants*100) : 0 }}%"></div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-{{ $prog->status === 'upcoming' ? 'in_progress' : ($prog->status === 'completed' ? 'completed' : ($prog->status === 'cancelled' ? 'cancelled' : 'active')) }}">
                        {{ $prog->status }}
                    </span>
                </td>
                <td>
                    <button class="btn btn-secondary btn-xs" onclick="openModal('edit-prog-{{ $prog->id }}')">
                        <i data-lucide="pencil" style="width:12px;height:12px"></i>
                    </button>
                </td>
            </tr>
            {{-- Edit Modal --}}
            <div class="modal-backdrop" id="edit-prog-{{ $prog->id }}">
                <div class="modal">
                    <div class="modal-header"><h3>Edit Program</h3><button class="modal-close" onclick="closeModal('edit-prog-{{ $prog->id }}')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.training.update', $prog) }}">
                            @csrf @method('PUT')
                            <div class="form-group"><label>Title</label><input type="text" name="title" value="{{ $prog->title }}" class="form-control" required></div>
                            <div class="form-row">
                                <div class="form-group"><label>Scheduled Date</label><input type="datetime-local" name="date_scheduled" value="{{ $prog->date_scheduled?->format('Y-m-d\TH:i') }}" class="form-control"></div>
                                <div class="form-group"><label>Location</label><input type="text" name="location" value="{{ $prog->location }}" class="form-control"></div>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    @foreach(['upcoming','ongoing','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $prog->status==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px"></i> Save</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <tr><td colspan="8"><div class="empty-state"><i data-lucide="graduation-cap"></i><p>No training programs found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $programs->links('vendor.pagination.custom') }}</div>
</div>

{{-- Add Training Modal --}}
<div class="modal-backdrop" id="modal-add-training">
    <div class="modal">
        <div class="modal-header"><h3>Add Training Program</h3><button class="modal-close" onclick="closeModal('modal-add-training')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.training.store') }}">
                @csrf
                <div class="form-group"><label>Program Title *</label><input type="text" name="title" class="form-control" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Type *</label>
                        <select name="type" class="form-control" required>
                            @foreach(['basic','advanced','certification','drill','online'] as $t)
                            <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control">
                            @foreach(['upcoming','ongoing','completed','cancelled'] as $s)
                            <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Scheduled Date</label><input type="datetime-local" name="date_scheduled" class="form-control"></div>
                    <div class="form-group"><label>Max Participants *</label><input type="number" name="max_participants" value="20" class="form-control" min="1" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Location</label><input type="text" name="location" class="form-control"></div>
                    <div class="form-group">
                        <label>Trainer</label>
                        <select name="trainer_id" class="form-control">
                            <option value="">— None —</option>
                            @foreach($trainers as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i data-lucide="plus" style="width:14px;height:14px"></i> Create Program</button>
            </form>
        </div>
    </div>
</div>
@endsection
