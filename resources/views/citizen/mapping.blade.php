@extends('layouts.app')
@section('title','Evacuation Map')
@section('page-title','Evacuation Map')
@section('sidebar-nav')@include('citizen._nav')@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    #map { height: 420px; }
    @media (max-width: 768px) {
        #map { height: 55vw; min-height: 240px; max-height: 360px; }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div><h1>Evacuation Map</h1><div class="bc">Citizen / Map</div></div>
</div>

<div class="info-box blue" style="margin-bottom:16px">
    <i data-lucide="info"></i>
    <span>🔴 Red = active incidents &nbsp;·&nbsp; 🟢 Green = open shelters &nbsp;·&nbsp; 🟡 Yellow = full shelters</span>
</div>

<div id="map" style="margin-bottom:20px;border-radius:var(--r);border:1px solid var(--border)"></div>

<div class="card">
    <div class="card-header"><h2><i data-lucide="map-pin"></i> Evacuation Shelters</h2></div>
    {{-- Mobile card list --}}
    <div id="shelter-cards">
        @forelse($shelters as $s)
        <div style="padding:13px 16px;border-bottom:1px solid rgba(23,32,48,.8)">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                <div style="min-width:0">
                    <div style="font-weight:600;font-size:.84rem;color:var(--text-bright)">{{ $s->name }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis">{{ $s->location }}</div>
                    @if($s->contact_person || $s->contact_no)
                    <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px">
                        📞 {{ $s->contact_person ?? '' }}{{ $s->contact_no ? ' · '.$s->contact_no : '' }}
                    </div>
                    @endif
                </div>
                <span class="badge badge-{{ $s->status==='open'?'completed':($s->status==='full'?'high':'cancelled') }}" style="flex-shrink:0">
                    {{ $s->status }}
                </span>
            </div>
            <div style="margin-top:8px">
                <div class="progress-bar" style="margin-bottom:4px">
                    <div class="fill {{ $s->getOccupancyPercent()>=90?'red':($s->getOccupancyPercent()>=60?'yellow':'green') }}"
                         style="width:{{ $s->getOccupancyPercent() }}%"></div>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.7rem;color:var(--text-muted)">
                    <span>{{ $s->current_occupancy }} / {{ $s->capacity }} occupants</span>
                    <span style="color:{{ $s->getOccupancyPercent()>=90 ? 'var(--red)' : 'var(--green)' }}">
                        {{ $s->capacity - $s->current_occupancy }} slots left
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state" style="padding:30px"><i data-lucide="map-pin"></i><p>No shelters listed.</p></div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
const map = L.map('map').setView([7.0731, 125.6128], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors', maxZoom: 18
}).addTo(map);

function makeIcon(color) {
    return L.divIcon({
        html: `<div style="width:24px;height:24px;background:${color};border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid white;box-shadow:0 2px 8px rgba(0,0,0,.4)"></div>`,
        iconSize:[24,24], iconAnchor:[12,24], popupAnchor:[0,-26], className:''
    });
}

@foreach($incidents as $inc)
L.marker([{{ $inc->latitude }}, {{ $inc->longitude }}], { icon: makeIcon('#f85149') })
    .addTo(map)
    .bindPopup(`<div style="font-family:system-ui,sans-serif;min-width:160px">
        <strong>{{ addslashes($inc->title) }}</strong><br>
        <span style="font-size:11px;color:#888">{{ addslashes($inc->location) }}</span><br>
        <span style="font-size:11px;font-weight:600;color:#f85149;text-transform:uppercase">⚠ {{ $inc->severity }} {{ $inc->type }}</span>
    </div>`);
@endforeach

@foreach($shelters as $s)
@if($s->latitude && $s->longitude)
L.marker([{{ $s->latitude }}, {{ $s->longitude }}],
    { icon: makeIcon('{{ $s->status === "open" ? "#3fb950" : ($s->status === "full" ? "#d29922" : "#7d8590") }}') })
    .addTo(map)
    .bindPopup(`<div style="font-family:system-ui,sans-serif;min-width:160px">
        <strong>{{ addslashes($s->name) }}</strong><br>
        <span style="font-size:11px;color:#888">{{ addslashes($s->location) }}</span><br>
        <span style="font-size:11px">{{ $s->current_occupancy }}/{{ $s->capacity }} occupants</span>
        @if($s->contact_no)<br><span style="font-size:11px;color:#888">📞 {{ $s->contact_no }}</span>@endif
    </div>`);
@endif
@endforeach
</script>
@endpush