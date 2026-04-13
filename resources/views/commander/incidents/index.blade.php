@extends('layouts.app')
@section('title','Incidents')
@section('page-title','Incident Management')
@section('sidebar-nav')@include('commander._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>Incidents</h1><div class="bc">Commander / Incidents</div></div>
    <a href="{{ route('commander.incidents.create') }}" class="btn btn-primary"><i data-lucide="plus" style="width:14px;height:14px"></i> New Incident</a>
</div>
<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="margin:0">
            <select name="status" class="form-control">
                <option value="">All Status</option>
                @foreach(['open','active','contained','closed'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="severity" class="form-control">
                <option value="">All Severity</option>
                @foreach(['low','moderate','high','critical'] as $s)
                <option value="{{ $s }}" {{ request('severity')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit"><i data-lucide="filter" style="width:13px;height:13px"></i> Filter</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Incident</th><th>Type</th><th>Severity</th><th>Status</th><th>Commander</th><th>Reported</th><th></th></tr></thead>
            <tbody>
            @forelse($incidents as $inc)
            <tr>
                <td>
                    <div style="font-weight:600;font-size:.83rem">{{ $inc->title }}</div>
                    <div style="font-size:.7rem;color:var(--text-muted)"><i data-lucide="map-pin" style="width:11px;height:11px;display:inline"></i> {{ $inc->location }}</div>
                </td>
                <td>
                    <div class="inc-icon {{ $inc->type }}" style="width:28px;height:28px">
                        <i data-lucide="{{ $inc->getTypeIcon() }}" style="width:13px;height:13px"></i>
                    </div>
                </td>
                <td><span class="badge badge-{{ $inc->severity }}">{{ $inc->severity }}</span></td>
                <td><span class="badge badge-{{ $inc->status }}">{{ $inc->status }}</span></td>
                <td style="font-size:.78rem">{{ $inc->commander?->name ?? '—' }}</td>
                <td style="font-size:.75rem;color:var(--text-muted)">{{ $inc->date_reported->format('M d, H:i') }}</td>
                <td><a href="{{ route('commander.incidents.show', $inc) }}" class="btn btn-secondary btn-xs"><i data-lucide="eye" style="width:12px;height:12px"></i> View</a></td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i data-lucide="flame"></i><p>No incidents.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $incidents->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>
@endsection
