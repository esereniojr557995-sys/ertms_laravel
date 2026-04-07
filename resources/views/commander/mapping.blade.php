{{-- commander/mapping.blade.php --}}
@extends('layouts.app')
@section('title','Live Map')
@section('page-title','Live Mapping')
@section('sidebar-nav')@include('commander._nav')@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
<div class="page-header">
    <div><h1>Live Map</h1><div class="breadcrumb">Commander / Mapping</div></div>
</div>

<div class="grid-2" style="margin-bottom:20px;align-items:start">
    <div style="grid-column:1/-1">
        <div id="map"></div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><h2><i data-lucide="flame" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Active Incidents on Map</h2></div>
        <div class="card-body">
            @forelse($incidents as $inc)
            <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid rgba(48,54,61,.5)">
                <div class="inc-icon {{ $inc->type }}" style="width:28px;height:28px"><i data-lucide="{{ $inc->getTypeIcon() }}" style="width:13px;height:13px"></i></div>
                <div style="flex:1">
                    <div style="font-weight:600;font-size:.82rem">{{ $inc->title }}</div>
                    <div style="font-size:.7rem;color:var(--text-muted)">{{ $inc->location }}</div>
                </div>
                <span class="badge badge-{{ $inc->severity }}">{{ $inc->severity }}</span>
            </div>
            @empty
            <div class="empty-state" style="padding:16px"><p>No active incidents with coordinates.</p></div>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2><i data-lucide="map-pin" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Evacuation Shelters</h2></div>
        <div class="card-body">
            @forelse($shelters as $s)
            <div style="padding:8px 0;border-bottom:1px solid rgba(48,54,61,.5)">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div style="font-weight:600;font-size:.82rem">{{ $s->name }}</div>
                    <span class="badge badge-{{ $s->status === 'open' ? 'completed' : ($s->status === 'full' ? 'high' : 'cancelled') }}">{{ $s->status }}</span>
                </div>
                <div style="font-size:.72rem;color:var(--text-muted)">{{ $s->location }}</div>
                <div style="margin-top:5px">
                    <div class="progress-bar"><div class="fill {{ $s->getOccupancyPercent()>=90?'red':($s->getOccupancyPercent()>=60?'yellow':'green') }}" style="width:{{ $s->getOccupancyPercent() }}%"></div></div>
                    <div style="font-size:.68rem;color:var(--text-muted);margin-top:2px">{{ $s->current_occupancy }}/{{ $s->capacity }} occupants</div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:16px"><p>No shelters registered.</p></div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
const map = L.map('map').setView([7.0731, 125.6128], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 18
}).addTo(map);

// Custom icon factory
function makeIcon(color) {
    return L.divIcon({
        html: `<div style="width:28px;height:28px;background:${color};border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid white;box-shadow:0 2px 8px rgba(0,0,0,.4)"></div>`,
        iconSize:[28,28], iconAnchor:[14,28], popupAnchor:[0,-30], className:''
    });
}

const incidentColors = { fire:'#f85149', flood:'#388bfd', earthquake:'#d29922', medical:'#3fb950', rescue:'#58a6ff', hazmat:'#e85d04', wind:'#bc8cff', other:'#7d8590' };
const severityColors = { low:'#3fb950', moderate:'#d29922', high:'#f85149', critical:'#ff4040' };

@foreach($incidents as $inc)
L.marker([{{ $inc->latitude }}, {{ $inc->longitude }}], { icon: makeIcon(incidentColors['{{ $inc->type }}'] || '#7d8590') })
    .addTo(map)
    .bindPopup(`<div style="font-family:Inter,sans-serif;min-width:180px"><strong style="font-size:13px">{{ $inc->title }}</strong><br><span style="color:#666;font-size:11px">{{ $inc->location }}</span><br><span style="background:{{ severityColors[$inc->severity] ?? '#888' }};color:#fff;padding:2px 7px;border-radius:10px;font-size:10px;font-weight:600;text-transform:uppercase">{{ $inc->severity }}</span> <span style="font-size:11px;color:#444">{{ $inc->status }}</span></div>`);
@endforeach

@foreach($shelters as $s)
@if($s->latitude && $s->longitude)
L.marker([{{ $s->latitude }}, {{ $s->longitude }}], { icon: makeIcon('{{ $s->status === "open" ? "#3fb950" : ($s->status === "full" ? "#d29922" : "#7d8590") }}') })
    .addTo(map)
    .bindPopup(`<div style="font-family:Inter,sans-serif;min-width:180px"><strong style="font-size:13px">{{ $s->name }}</strong><br><span style="color:#666;font-size:11px">{{ $s->location }}</span><br><span style="font-size:11px">Occupancy: {{ $s->current_occupancy }}/{{ $s->capacity }}</span></div>`);
@endif
@endforeach
</script>
@endpush
@endsection
