@extends('layouts.app')
@section('title','Alerts')
@section('page-title','Alerts & Notifications')
@section('sidebar-nav')@include('responder._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>Alerts</h1><div class="bc">Responder / Alerts</div></div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Alert</th><th>Type</th><th>Severity</th><th>Sent By</th><th>Time</th></tr></thead>
            <tbody>
            @forelse($alerts as $alert)
            <tr>
                <td>
                    <div style="font-weight:600;font-size:.83rem">{{ $alert->title }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);max-width:320px">{{ $alert->message }}</div>
                </td>
                <td><span class="badge badge-info">{{ $alert->type }}</span></td>
                <td>
                    <div style="display:flex;align-items:center;gap:6px">
                        <div class="alert-dot {{ $alert->severity }}"></div>
                        <span class="badge badge-{{ $alert->severity }}">{{ $alert->severity }}</span>
                    </div>
                </td>
                <td style="font-size:.78rem">{{ $alert->sender->name }}</td>
                <td style="font-size:.75rem;color:var(--text-muted)">{{ $alert->created_at->format('M d, H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state"><i data-lucide="bell-off"></i><p>No alerts.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $alerts->links('vendor.pagination.custom') }}</div>
</div>
@endsection
