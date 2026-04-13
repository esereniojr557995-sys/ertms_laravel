{{-- commander/alerts/index.blade.php --}}
@extends('layouts.app')
@section('title','Alerts')
@section('page-title','Alerts')
@section('sidebar-nav')@include('commander._nav')@endsection
@section('content')
<div class="page-header">
    <div><h1>Alerts</h1><div class="bc">Commander / Alerts</div></div>
    <button class="btn btn-primary" onclick="openModal('modal-alert')"><i data-lucide="send" style="width:14px;height:14px"></i> Send Alert</button>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Alert</th><th>Severity</th><th>Type</th><th>Sent By</th><th>Time</th></tr></thead>
            <tbody>
            @forelse($alerts as $alert)
            <tr>
                <td><div style="font-weight:600;font-size:.83rem">{{ $alert->title }}</div><div style="font-size:.72rem;color:var(--text-muted)">{{ Str::limit($alert->message,80) }}</div></td>
                <td><div style="display:flex;align-items:center;gap:6px"><div class="alert-dot {{ $alert->severity }}"></div><span class="badge badge-{{ $alert->severity }}">{{ $alert->severity }}</span></div></td>
                <td><span class="badge badge-info">{{ $alert->type }}</span></td>
                <td style="font-size:.78rem">{{ $alert->sender->name }}</td>
                <td style="font-size:.75rem;color:var(--text-muted)">{{ $alert->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state"><i data-lucide="bell-off"></i><p>No alerts.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $alerts->links('vendor.pagination.custom') }}</div>
</div>
<div class="modal-backdrop" id="modal-alert">
    <div class="modal">
        <div class="modal-header"><h3>Send Alert</h3><button class="modal-close" onclick="closeModal('modal-alert')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
        <div class="modal-body">
            <form method="POST" action="{{ route('commander.alerts.store') }}">
                @csrf
                <div class="form-group"><label>Title *</label><input type="text" name="title" class="form-control" required></div>
                <div class="form-group"><label>Message *</label><textarea name="message" class="form-control" rows="3" required></textarea></div>
                <div class="form-row">
                    <div class="form-group"><label>Severity</label><select name="severity" class="form-control"><option value="info">Info</option><option value="warning">Warning</option><option value="high">High</option><option value="critical">Critical</option></select></div>
                    <div class="form-group"><label>Type</label><select name="type" class="form-control"><option value="evacuation">Evacuation</option><option value="weather">Weather</option><option value="incident">Incident</option><option value="public">Public</option></select></div>
                </div>
                <div class="form-group"><label>Audience</label><div style="display:flex;flex-direction:column;gap:6px"><div class="form-check"><input type="checkbox" name="target_audience[]" value="responders" id="a1"><label for="a1">Responders</label></div><div class="form-check"><input type="checkbox" name="target_audience[]" value="citizens" id="a2"><label for="a2">Citizens</label></div><div class="form-check"><input type="checkbox" name="target_audience[]" value="all" id="a3"><label for="a3">All</label></div></div></div>
                <div class="form-group"><label>Link Incident</label><select name="incident_id" class="form-control"><option value="">— None —</option>@foreach($incidents as $i)<option value="{{ $i->id }}">{{ $i->title }}</option>@endforeach</select></div>
                <button type="submit" class="btn btn-primary"><i data-lucide="send" style="width:14px;height:14px"></i> Send</button>
            </form>
        </div>
    </div>
</div>
@endsection
