@extends('layouts.app')
@section('title','Alerts')
@section('page-title','Alerts & Advisories')
@section('sidebar-nav')@include('citizen._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>Alerts & Advisories</h1><div class="bc">Citizen / Alerts</div></div>
</div>

<div class="card">
    {{-- Mobile: card list. Desktop: table --}}
    @forelse($alerts as $alert)
    <div style="padding:14px 16px;border-bottom:1px solid rgba(23,32,48,.8)">
        <div style="display:flex;align-items:flex-start;gap:10px">
            <div class="alert-dot {{ $alert->severity }}" style="margin-top:6px;flex-shrink:0"></div>
            <div style="flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-bottom:4px">
                    <span style="font-weight:600;font-size:.88rem;color:var(--text-bright)">{{ $alert->title }}</span>
                    <span class="badge badge-{{ $alert->severity }}">{{ $alert->severity }}</span>
                </div>
                <div style="font-size:.8rem;color:var(--text);line-height:1.55;margin-bottom:6px">{{ $alert->message }}</div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <span class="badge badge-info" style="text-transform:capitalize">{{ $alert->type }}</span>
                    <span style="font-size:.72rem;color:var(--text-muted)">{{ $alert->created_at->format('M d, Y H:i') }}</span>
                    <span style="font-size:.72rem;color:var(--text-dim)">{{ $alert->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state" style="padding:40px">
        <i data-lucide="bell-off"></i>
        <p>No alerts issued.</p>
    </div>
    @endforelse
    <div style="padding:16px 20px">{{ $alerts->links('vendor.pagination.custom') }}</div>
</div>
@endsection