@extends('layouts.app')
@section('title','Alerts')
@section('page-title','Alerts & Advisories')
@section('sidebar-nav')@include('citizen._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>Alerts & Advisories</h1><div class="breadcrumb">Citizen / Alerts</div></div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Alert</th><th>Type</th><th>Severity</th><th>Issued</th></tr></thead>
            <tbody>
            @forelse($alerts as $alert)
            <tr>
                <td>
                    <div style="display:flex;align-items:flex-start;gap:10px">
                        <div class="alert-dot {{ $alert->severity }}" style="margin-top:5px;flex-shrink:0"></div>
                        <div>
                            <div style="font-weight:600;font-size:.85rem">{{ $alert->title }}</div>
                            <div style="font-size:.78rem;color:var(--text-muted);margin-top:4px;line-height:1.5;max-width:480px">{{ $alert->message }}</div>
                        </div>
                    </div>
                </td>
                <td><span class="badge badge-info" style="text-transform:capitalize">{{ $alert->type }}</span></td>
                <td>
                    <span class="badge badge-{{ $alert->severity }}">{{ $alert->severity }}</span>
                </td>
                <td style="font-size:.75rem;color:var(--text-muted)">
                    {{ $alert->created_at->format('M d, Y H:i') }}<br>
                    <span style="font-size:.7rem">{{ $alert->created_at->diffForHumans() }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="4"><div class="empty-state"><i data-lucide="bell-off"></i><p>No alerts issued.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $alerts->links('vendor.pagination.custom') }}</div>
</div>
@endsection
