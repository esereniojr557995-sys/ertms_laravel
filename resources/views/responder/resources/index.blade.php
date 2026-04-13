{{-- responder/resources/index.blade.php --}}
@extends('layouts.app')
@section('title','Resources')
@section('page-title','Resource Overview')
@section('sidebar-nav')@include('responder._nav')@endsection
@section('content')
<div class="page-header">
    <div><h1>Resources</h1><div class="bc">Responder / Resources</div></div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Resource</th><th>Type</th><th>Quantity</th><th>Stock Level</th><th>Location</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($resources as $r)
            <tr>
                <td style="font-weight:600">{{ $r->name }}</td>
                <td style="font-size:.78rem;text-transform:capitalize">{{ str_replace('_',' ',$r->type) }}</td>
                <td>
                    <span style="font-weight:600;color:{{ $r->isLow()?'var(--red)':'var(--text)' }}">{{ $r->quantity }}</span>
                    <span style="color:var(--text-muted);font-size:.75rem"> {{ $r->unit }}</span>
                    @if($r->isLow())<span class="badge badge-high" style="margin-left:4px">LOW</span>@endif
                </td>
                <td style="min-width:100px">
                    @php $pct = $r->min_threshold > 0 ? min(100, round($r->quantity / max($r->min_threshold * 2, 1) * 100)) : 100; @endphp
                    <div class="progress-bar"><div class="fill {{ $pct<40?'red':($pct<70?'yellow':'green') }}" style="width:{{ $pct }}%"></div></div>
                    <div style="font-size:.68rem;color:var(--text-muted);margin-top:2px">min: {{ $r->min_threshold }}</div>
                </td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $r->location ?? '—' }}</td>
                <td><span class="badge badge-{{ $r->status }}">{{ str_replace('_',' ',$r->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i data-lucide="package"></i><p>No resources.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $resources->links('vendor.pagination.custom') }}</div>
</div>
@endsection
