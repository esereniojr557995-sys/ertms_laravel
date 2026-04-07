{{-- commander/resources/index.blade.php --}}
@extends('layouts.app')
@section('title','Resources')
@section('page-title','Resource Overview')
@section('sidebar-nav')@include('commander._nav')@endsection
@section('content')
<div class="page-header">
    <div><h1>Resources</h1><div class="breadcrumb">Commander / Resources</div></div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Resource</th><th>Type</th><th>Qty</th><th>Stock</th><th>Location</th><th>Status</th><th>Update</th></tr></thead>
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
                <td style="min-width:90px">
                    @php $pct = $r->min_threshold > 0 ? min(100, round($r->quantity / max($r->min_threshold * 2, 1) * 100)) : 100; @endphp
                    <div class="progress-bar"><div class="fill {{ $pct<40?'red':($pct<70?'yellow':'green') }}" style="width:{{ $pct }}%"></div></div>
                </td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $r->location ?? '—' }}</td>
                <td><span class="badge badge-{{ $r->status }}">{{ str_replace('_',' ',$r->status) }}</span></td>
                <td>
                    <form method="POST" action="{{ route('commander.resources.update', $r) }}" style="display:flex;gap:6px;align-items:center">
                        @csrf @method('PUT')
                        <input type="number" name="quantity" value="{{ $r->quantity }}" class="form-control" style="width:70px;padding:4px 6px" min="0">
                        <select name="status" class="form-control" style="width:auto;padding:4px 6px;font-size:.75rem">
                            @foreach(['available','in_use','maintenance','depleted'] as $s)
                            <option value="{{ $s }}" {{ $r->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-xs"><i data-lucide="save" style="width:12px;height:12px"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i data-lucide="package"></i><p>No resources.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $resources->links('vendor.pagination.custom') }}</div>
</div>
@endsection
