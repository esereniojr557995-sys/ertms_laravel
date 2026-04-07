@extends('layouts.app')
@section('title','Live Map')
@section('page-title','Live Map')
@section('sidebar-nav')@include('responder._nav')@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
<div class="page-header">
    <div><h1>Live Map</h1><div class="breadcrumb">Responder / Mapping</div></div>
</div>

<div id="map" style="margin-bottom:20px"></div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><h2><i data-lucide="flame" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Active Incidents</h2></div>
        <div class="card-body" style="padding:0">
            @forelse($incidents as $inc)
            <div style="padding:12px 16px;border-bottom:1px solid rgba(48,54,61,.5);display:flex;align-items:center;gap:10px">
                <div class="inc-icon {{ $inc->type }}" style="width:28px;height:28px"><i data-lucide="{{ $inc->getTypeIcon() }}" style="width:13px;height:13px"></i></div>
                <div>
                    <div style="font-weight:600;font-size:.82rem">{{ $inc->title }}</div>
                    <div style="font-size:.7rem;color:var(--text-muted)">{{ $inc->location }}</div>
                </div>
                <span class="badge badge-{{ $inc->severity }}" style="margin-left:auto;flex-shrink:0">{{ $inc->severity }}</span>
            </div>
            @empty
            <div class="empty-state" style="padding:20px"><p>No active incidents with coordinates.</p></div>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2><i data-lucide="map-pin" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Open Shelters</h2></div>
        <div class="card-body" style="padding:0">
            @forelse($shelters as $s)
            <div style="padding:12px 16px;border-bottom:1px solid rgba(48,54,61,.5)">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div style="font-weight:600;font-size:.82rem">{{ $s->name }}</div>
                    <span class="badge badge-completed">{{ $s->status }}</span>
                </div>
                <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px">{{ $s->location }}</div>
                <div style="margin-top:6px">
                    <div class="progress-bar">
                        <div class="fill {{ $s->getOccupancyPercent() >= 90 ? 'red' : ($s->getOccupancyPercent() >= 60 ? 'yellow' : 'green') }}" style="width:{{ $s->getOccupancyPercent() }}%"></div>
                    </div>
                    <div style="font-size:.68rem;color:var(--text-muted);margin-top:2px">{{ $s->current_occupancy }} / {{ $s->capacity }} occupants ({{ $s->getOccupancyPercent() }}%)</div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:20px"><p>No open shelters.</p></div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
const map = L.map('map').setView([7.0731, 125.6128], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap contributors', maxZoom:18 }).addTo(map);

function makeIcon(color) {
    return L.divIcon({
        html: `<div style="width:26px;height:26px;background:${color};border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid white;box-shadow:0 2px 8px rgba(0,0,0,.4)"></div>`,
        iconSize:[26,26], iconAnchor:[13,26], popupAnchor:[0,-28], className:''
    });
}

const incidentColors = { fire:'#f85149', flood:'#388bfd', earthquake:'#d29922', medical:'#3fb950', rescue:'#58a6ff', hazmat:'#e85d04', wind:'#bc8cff', other:'#7d8590' };

@foreach($incidents as $inc)
L.marker([{{ $inc->latitude }}, {{ $inc->longitude }}], { icon: makeIcon(incidentColors['{{ $inc->type }}'] || '#888') })
    .addTo(map)
    .bindPopup(`<strong>{{ $inc->title }}</strong><br><span style="font-size:11px">{{ $inc->location }}</span><br><span style="font-size:11px;font-weight:600;text-transform:uppercase;color:{{ $inc->severity==='critical'?'#f85149':'#d29922' }}">{{ $inc->severity }}</span>`);
@endforeach

@foreach($shelters as $s)
@if($s->latitude && $s->longitude)
L.marker([{{ $s->latitude }}, {{ $s->longitude }}], { icon: makeIcon('#3fb950') })
    .addTo(map)
    .bindPopup(`<strong>{{ $s->name }}</strong><br><span style="font-size:11px">{{ $s->location }}</span><br><span style="font-size:11px">Capacity: {{ $s->current_occupancy }}/{{ $s->capacity }}</span>`);
@endif
@endforeach
</script>
@endpush
@endsection
