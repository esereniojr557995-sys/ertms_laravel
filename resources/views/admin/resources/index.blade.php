{{-- ============================================================
     resources/views/admin/resources/index.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title','Resources')
@section('page-title','Resource & Inventory Management')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.resources') }}" class="active"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}"><i data-lucide="bell"></i> Alerts</a>
<a href="{{ route('admin.comms') }}" class="{{ request()->routeIs('admin.comms*') ? 'active' : '' }}">
    <i data-lucide="message-square"></i> Communications
</a>
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
    <div><h1>Resources</h1><div class="breadcrumb">Admin / Resources</div></div>
    <button class="btn btn-primary" onclick="openModal('modal-add-resource')"><i data-lucide="plus" style="width:14px;height:14px"></i> Add Resource</button>
</div>

@if($lowStock > 0)
<div class="warning-strip">
    <i data-lucide="alert-triangle" style="width:16px;height:16px"></i>
    <strong>{{ $lowStock }} resource(s)</strong> are at or below minimum stock threshold.
</div>
@endif

<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="margin:0">
            <select name="type" class="form-control">
                <option value="">All Types</option>
                @foreach(['equipment','vehicle','medical_supply','personnel','other'] as $t)
                <option value="{{ $t }}" {{ request('type')==$t?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit"><i data-lucide="filter" style="width:13px;height:13px"></i> Filter</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Resource</th><th>Type</th><th>Qty / Unit</th><th>Stock Level</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($resources as $r)
            <tr>
                <td style="font-weight:600;font-size:.83rem">{{ $r->name }}</td>
                <td style="font-size:.78rem;text-transform:capitalize">{{ str_replace('_',' ',$r->type) }}</td>
                <td>
                    <span style="font-weight:600;color:{{ $r->isLow() ? 'var(--red)' : 'var(--text)' }}">{{ $r->quantity }}</span>
                    <span style="color:var(--text-muted);font-size:.75rem"> {{ $r->unit }}</span>
                    @if($r->isLow())<span class="badge badge-high" style="margin-left:4px">LOW</span>@endif
                </td>
                <td style="min-width:100px">
                    @php $pct = $r->min_threshold > 0 ? min(100, round($r->quantity / max($r->min_threshold * 2, 1) * 100)) : 100; @endphp
                    <div class="progress-bar">
                        <div class="fill {{ $pct < 40 ? 'red' : ($pct < 70 ? 'yellow' : 'green') }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <div style="font-size:.68rem;color:var(--text-muted);margin-top:2px">min: {{ $r->min_threshold }}</div>
                </td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $r->location ?? '—' }}</td>
                <td><span class="badge badge-{{ $r->status }}">{{ str_replace('_',' ',$r->status) }}</span></td>
                <td>
                    <div style="display:flex;gap:6px">
                        <button class="btn btn-secondary btn-xs" onclick="openModal('edit-res-{{ $r->id }}')"><i data-lucide="pencil" style="width:12px;height:12px"></i></button>
                        <form id="del-res-{{ $r->id }}" method="POST" action="{{ route('admin.resources.destroy',$r) }}">@csrf @method('DELETE')</form>
                        <button class="btn btn-danger btn-xs" onclick="confirmDelete('del-res-{{ $r->id }}')"><i data-lucide="trash-2" style="width:12px;height:12px"></i></button>
                    </div>
                </td>
            </tr>
            {{-- Edit Modal --}}
            <div class="modal-backdrop" id="edit-res-{{ $r->id }}">
                <div class="modal">
                    <div class="modal-header"><h3>Edit: {{ $r->name }}</h3><button class="modal-close" onclick="closeModal('edit-res-{{ $r->id }}')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.resources.update',$r) }}">
                            @csrf @method('PUT')
                            <div class="form-row">
                                <div class="form-group"><label>Name</label><input type="text" name="name" value="{{ $r->name }}" class="form-control" required></div>
                                <div class="form-group"><label>Quantity</label><input type="number" name="quantity" value="{{ $r->quantity }}" class="form-control" min="0" required></div>
                            </div>
                            <div class="form-row">
                                <div class="form-group"><label>Min Threshold</label><input type="number" name="min_threshold" value="{{ $r->min_threshold }}" class="form-control" min="0" required></div>
                                <div class="form-group"><label>Unit</label><input type="text" name="unit" value="{{ $r->unit }}" class="form-control" required></div>
                            </div>
                            <div class="form-row">
                                <div class="form-group"><label>Location</label><input type="text" name="location" value="{{ $r->location }}" class="form-control"></div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        @foreach(['available','in_use','maintenance','depleted'] as $s)
                                        <option value="{{ $s }}" {{ $r->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px"></i> Save</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i data-lucide="package"></i><p>No resources found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $resources->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>

{{-- Add Resource Modal --}}
<div class="modal-backdrop" id="modal-add-resource">
    <div class="modal">
        <div class="modal-header"><h3>Add Resource</h3><button class="modal-close" onclick="closeModal('modal-add-resource')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.resources.store') }}">
                @csrf
                <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Type *</label>
                        <select name="type" class="form-control" required>
                            @foreach(['equipment','vehicle','medical_supply','personnel','other'] as $t)
                            <option value="{{ $t }}">{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            @foreach(['available','in_use','maintenance','depleted'] as $s)
                            <option value="{{ $s }}">{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Quantity *</label><input type="number" name="quantity" class="form-control" min="0" required></div>
                    <div class="form-group"><label>Min Threshold *</label><input type="number" name="min_threshold" value="5" class="form-control" min="0" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Unit *</label><input type="text" name="unit" class="form-control" placeholder="pcs / unit / kits" required></div>
                    <div class="form-group"><label>Location</label><input type="text" name="location" class="form-control"></div>
                </div>
                <button type="submit" class="btn btn-primary"><i data-lucide="plus" style="width:14px;height:14px"></i> Add Resource</button>
            </form>
        </div>
    </div>
</div>
@endsection
