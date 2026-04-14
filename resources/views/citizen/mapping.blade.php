@extends('layouts.app')
@section('title','Evacuation Map')
@section('page-title','Evacuation Map & Shelters')
@section('sidebar-nav')@include('citizen._nav')@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
<div class="page-header">
    <div><h1>Evacuation Map</h1><div class="bc">Citizen / Map</div></div>
</div>

<div style="background:rgba(56,139,253,.08);border:1px solid rgba(56,139,253,.25);border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px">
    <i data-lucide="info" style="width:16px;height:16px;color:var(--blue);flex-shrink:0"></i>
    <span style="font-size:.82rem">Red pins = active incidents. Green pins = open shelters. Yellow pins = full shelters.</span>
</div>

<div id="map" style="margin-bottom:20px"></div>

<div class="card">
    <div class="card-header"><h2><i data-lucide="map-pin" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Evacuation Shelters</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Shelter</th><th>Location</th><th>Capacity</th><th>Occupancy</th><th>Contact</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($shelters as $s)
            <tr>
                <td style="font-weight:600">{{ $s->name }}</td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $s->location }}</td>
                <td style="font-size:.82rem">{{ number_format($s->capacity) }}</td>
                <td style="min-width:120px">
                    <div class="progress-bar">
                        <div class="fill {{ $s->getOccupancyPercent()>=90?'red':($s->getOccupancyPercent()>=60?'yellow':'green') }}" style="width:{{ $s->getOccupancyPercent() }}%"></div>
                    </div>
                    <div style="font-size:.7rem;color:var(--text-muted);margin-top:2px">{{ $s->current_occupancy }} / {{ $s->capacity }}</div>
                </td>
                <td style="font-size:.78rem">
                    {{ $s->contact_person ?? '—' }}<br>
                    <span style="color:var(--text-muted)">{{ $s->contact_no ?? '' }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ $s->status==='open'?'completed':($s->status==='full'?'high':'cancelled') }}">{{ $s->status }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i data-lucide="map-pin"></i><p>No shelters listed.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
const map = L.map('map').setView([7.0731, 125.6128], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap contributors', maxZoom:18 }).addTo(map);

function makeIcon(color) {
    return L.divIcon({
        html: `<div style="width:24px;height:24px;background:${color};border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid white;box-shadow:0 2px 8px rgba(0,0,0,.4)"></div>`,
        iconSize:[24,24], iconAnchor:[12,24], popupAnchor:[0,-26], className:''
    });
}

@foreach($incidents as $inc)
L.marker([{{ $inc->latitude }}, {{ $inc->longitude }}], { icon: makeIcon('#f85149') })
    .addTo(map)
    .bindPopup(`<div style="font-family:Inter,sans-serif"><strong>{{ $inc->title }}</strong><br><span style="font-size:11px;color:#666">{{ $inc->location }}</span><br><span style="font-size:11px;font-weight:600;text-transform:uppercase;color:#f85149">⚠ {{ $inc->severity }} {{ $inc->type }}</span></div>`);
@endforeach

@foreach($shelters as $s)
@if($s->latitude && $s->longitude)
L.marker([{{ $s->latitude }}, {{ $s->longitude }}], { icon: makeIcon('{{ $s->status === "open" ? "#3fb950" : ($s->status === "full" ? "#d29922" : "#7d8590") }}') })
    .addTo(map)
    .bindPopup(`<div style="font-family:Inter,sans-serif"><strong>{{ $s->name }}</strong><br><span style="font-size:11px;color:#666">{{ $s->location }}</span><br><span style="font-size:11px">{{ $s->current_occupancy }}/{{ $s->capacity }} occupants</span><br>@if($s->contact_no)<span style="font-size:11px;color:#666">📞 {{ $s->contact_no }}</span>@endif</div>`);
@endif
@endforeach
</script>
@endpush
@endsection
