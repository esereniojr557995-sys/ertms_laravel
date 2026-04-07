{{-- commander/patients/index.blade.php --}}
@extends('layouts.app')
@section('title','Medical')
@section('page-title','Medical — Patients')
@section('sidebar-nav')@include('commander._nav')@endsection
@section('content')
<div class="page-header">
    <div><h1>Patients</h1><div class="breadcrumb">Commander / Medical</div></div>
    <button class="btn btn-primary" onclick="openModal('modal-add-patient')"><i data-lucide="user-plus" style="width:14px;height:14px"></i> Add Patient</button>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Patient</th><th>Triage</th><th>Incident</th><th>Status</th><th>Hospital</th><th>Update</th></tr></thead>
            <tbody>
            @forelse($patients as $p)
            <tr>
                <td><div style="font-weight:600">{{ $p->name }}</div><div style="font-size:.72rem;color:var(--text-muted)">{{ $p->age ?? '?' }} yrs · {{ $p->gender }}</div></td>
                <td><span class="badge badge-{{ $p->triage_level }}">{{ $p->triage_level }}</span></td>
                <td style="font-size:.78rem;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $p->incident->title }}</td>
                <td><span class="badge badge-{{ $p->status==='admitted'?'completed':($p->status==='on_scene'?'active':'in_progress') }}">{{ str_replace('_',' ',$p->status) }}</span></td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $p->hospital_name ?? '—' }}</td>
                <td>
                    <form method="POST" action="{{ route('commander.patients.update', $p) }}" style="display:flex;gap:4px;align-items:center">
                        @csrf @method('PUT')
                        <select name="status" class="form-control" style="width:auto;padding:4px 6px;font-size:.73rem">
                            @foreach(['on_scene','transported','admitted','discharged','deceased'] as $s)
                            <option value="{{ $s }}" {{ $p->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="triage_level" value="{{ $p->triage_level }}">
                        <button type="submit" class="btn btn-primary btn-xs"><i data-lucide="save" style="width:12px;height:12px"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i data-lucide="heart-pulse"></i><p>No patient records.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $patients->links('vendor.pagination.custom') }}</div>
</div>

<div class="modal-backdrop" id="modal-add-patient">
    <div class="modal">
        <div class="modal-header"><h3>Add Patient</h3><button class="modal-close" onclick="closeModal('modal-add-patient')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
        <div class="modal-body">
            <form method="POST" action="{{ route('commander.patients.store') }}">
                @csrf
                <div class="form-row"><div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div><div class="form-group"><label>Age</label><input type="number" name="age" class="form-control" min="0"></div></div>
                <div class="form-row">
                    <div class="form-group"><label>Gender</label><select name="gender" class="form-control"><option value="male">Male</option><option value="female">Female</option><option value="unknown">Unknown</option></select></div>
                    <div class="form-group"><label>Triage *</label><select name="triage_level" class="form-control" required>@foreach(['immediate','delayed','minor','expectant','deceased'] as $t)<option value="{{ $t }}">{{ ucfirst($t) }}</option>@endforeach</select></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Incident *</label><select name="incident_id" class="form-control" required><option value="">— Select —</option>@foreach($incidents as $i)<option value="{{ $i->id }}">{{ $i->title }}</option>@endforeach</select></div>
                    <div class="form-group"><label>Status *</label><select name="status" class="form-control">@foreach(['on_scene','transported','admitted','discharged','deceased'] as $s)<option value="{{ $s }}">{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select></div>
                </div>
                <div class="form-group"><label>Location Found</label><input type="text" name="location_found" class="form-control"></div>
                <div class="form-group"><label>Hospital</label><input type="text" name="hospital_name" class="form-control"></div>
                <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                <button type="submit" class="btn btn-primary"><i data-lucide="user-plus" style="width:14px;height:14px"></i> Add</button>
            </form>
        </div>
    </div>
</div>
@endsection
