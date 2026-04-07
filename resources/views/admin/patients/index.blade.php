@extends('layouts.app')
@section('title','Medical')
@section('page-title','Medical & Casualty Management')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}"><i data-lucide="bell"></i> Alerts</a>
<a href="{{ route('admin.patients') }}" class="active"><i data-lucide="heart-pulse"></i> Medical</a>
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
    <div><h1>Medical — Patients</h1><div class="breadcrumb">Admin / Medical</div></div>
    <button class="btn btn-primary" onclick="openModal('modal-add-patient')">
        <i data-lucide="user-plus" style="width:14px;height:14px"></i> Add Patient
    </button>
</div>

{{-- Triage Summary --}}
<div class="stat-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:24px">
    @php
    $triageCounts = $patients->getCollection()->groupBy('triage_level');
    $triageColors = ['immediate'=>'red','delayed'=>'yellow','minor'=>'green','expectant'=>'purple','deceased'=>''];
    @endphp
    @foreach(['immediate','delayed','minor','expectant','deceased'] as $tl)
    <div class="stat-card {{ $triageColors[$tl] ?? '' }}">
        <div class="sc-label">{{ ucfirst($tl) }}</div>
        <div class="sc-val" style="font-size:1.6rem">{{ $triageCounts[$tl]?->count() ?? 0 }}</div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="margin:0">
            <select name="triage" class="form-control">
                <option value="">All Triage</option>
                @foreach(['immediate','delayed','minor','expectant','deceased'] as $t)
                <option value="{{ $t }}" {{ request('triage')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit"><i data-lucide="filter" style="width:13px;height:13px"></i> Filter</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Patient</th><th>Triage</th><th>Incident</th><th>Medic</th><th>Hospital</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($patients as $p)
            <tr>
                <td>
                    <div style="font-weight:600">{{ $p->name }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted)">{{ $p->age ? $p->age.' yrs' : 'Age unknown' }} · {{ $p->gender }}</div>
                </td>
                <td><span class="badge badge-{{ $p->triage_level }}">{{ $p->triage_level }}</span></td>
                <td style="font-size:.78rem;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $p->incident->title }}</td>
                <td style="font-size:.78rem">{{ $p->medic?->name ?? '—' }}</td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $p->hospital_name ?? '—' }}</td>
                <td><span class="badge badge-{{ in_array($p->status,['admitted','discharged']) ? 'completed' : ($p->status=='on_scene'?'active':'in_progress') }}">{{ str_replace('_',' ',$p->status) }}</span></td>
                <td>
                    <button class="btn btn-secondary btn-xs" onclick="openModal('edit-pat-{{ $p->id }}')">
                        <i data-lucide="pencil" style="width:12px;height:12px"></i> Update
                    </button>
                </td>
            </tr>
            {{-- Edit Patient Modal --}}
            <div class="modal-backdrop" id="edit-pat-{{ $p->id }}">
                <div class="modal">
                    <div class="modal-header"><h3>Update: {{ $p->name }}</h3><button class="modal-close" onclick="closeModal('edit-pat-{{ $p->id }}')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.patients.update',$p) }}">
                            @csrf @method('PUT')
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Triage Level</label>
                                    <select name="triage_level" class="form-control">
                                        @foreach(['immediate','delayed','minor','expectant','deceased'] as $t)
                                        <option value="{{ $t }}" {{ $p->triage_level==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        @foreach(['on_scene','transported','admitted','discharged','deceased'] as $s)
                                        <option value="{{ $s }}" {{ $p->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Assign Medic</label>
                                    <select name="assigned_medic_id" class="form-control">
                                        <option value="">— None —</option>
                                        @foreach($medics as $m)
                                        <option value="{{ $m->id }}" {{ $p->assigned_medic_id==$m->id?'selected':'' }}>{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group"><label>Hospital</label><input type="text" name="hospital_name" value="{{ $p->hospital_name }}" class="form-control" placeholder="Hospital name"></div>
                            </div>
                            <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2">{{ $p->notes }}</textarea></div>
                            <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px"></i> Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i data-lucide="heart-pulse"></i><p>No patient records.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $patients->links('vendor.pagination.custom') }}</div>
</div>

{{-- Add Patient Modal --}}
<div class="modal-backdrop" id="modal-add-patient">
    <div class="modal">
        <div class="modal-header"><h3>Add Patient Record</h3><button class="modal-close" onclick="closeModal('modal-add-patient')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.patients.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="form-group"><label>Age</label><input type="number" name="age" class="form-control" min="0"></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Gender *</label>
                        <select name="gender" class="form-control"><option value="male">Male</option><option value="female">Female</option><option value="unknown">Unknown</option></select>
                    </div>
                    <div class="form-group">
                        <label>Triage Level *</label>
                        <select name="triage_level" class="form-control" required>
                            @foreach(['immediate','delayed','minor','expectant','deceased'] as $t)
                            <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Incident *</label>
                        <select name="incident_id" class="form-control" required>
                            <option value="">— Select —</option>
                            @foreach($incidents as $inc)<option value="{{ $inc->id }}">{{ $inc->title }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control">
                            @foreach(['on_scene','transported','admitted','discharged','deceased'] as $s)
                            <option value="{{ $s }}">{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group"><label>Location Found</label><input type="text" name="location_found" class="form-control"></div>
                <div class="form-group"><label>Hospital Name</label><input type="text" name="hospital_name" class="form-control"></div>
                <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                <button type="submit" class="btn btn-primary"><i data-lucide="user-plus" style="width:14px;height:14px"></i> Add Patient</button>
            </form>
        </div>
    </div>
</div>
@endsection
