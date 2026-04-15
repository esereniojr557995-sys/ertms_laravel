@extends('layouts.app')
@section('title','Shelters')
@section('page-title','Shelter Management')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.citizen_reports') }}" class="{{ request()->routeIs('admin.citizen_reports*') ? 'active' : '' }}">
    <i data-lucide="file-text"></i> Citizen Reports
</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}"><i data-lucide="bell"></i> Alerts</a>
<a href="{{ route('admin.comms') }}" class="{{ request()->routeIs('admin.comms*') ? 'active' : '' }}">
    <i data-lucide="message-square"></i> Communications
</a>
<a href="{{ route('admin.patients') }}"><i data-lucide="heart-pulse"></i> Medical</a>
<a href="{{ route('admin.training') }}"><i data-lucide="graduation-cap"></i> Training</a>
<a href="{{ route('admin.shelters') }}" class="active"><i data-lucide="map-pin"></i> Shelters</a>
<div class="sb-section">Management</div>
<a href="{{ route('admin.users') }}"><i data-lucide="users"></i> Users</a>
<a href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-2"></i> Reports</a>
<a href="{{ route('admin.audit_logs') }}"><i data-lucide="scroll-text"></i> Audit Logs</a>
<a href="{{ route('admin.settings') }}"><i data-lucide="settings"></i> Settings</a>
@endsection

@push('styles')
<style>
    .place-search-wrap { position: relative; }
    .place-search-wrap .search-icon {
        position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted); pointer-events: none;
        width: 14px; height: 14px;
    }
    .place-search-wrap .form-control { padding-left: 32px; }
    .place-search-wrap .clear-btn {
        position: absolute; right: 8px; top: 50%;
        transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: var(--text-muted); padding: 2px;
        display: none;
    }
    .place-search-wrap .clear-btn:hover { color: var(--text); }

    .place-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0; right: 0;
        background: var(--surface2);
        border: 1px solid var(--border2);
        border-radius: var(--r);
        z-index: 9999;
        max-height: 260px;
        overflow-y: auto;
        box-shadow: 0 12px 30px rgba(0,0,0,.5);
        display: none;
    }
    .place-dropdown.open { display: block; }
    .place-result {
        padding: 10px 13px;
        cursor: pointer;
        border-bottom: 1px solid var(--border);
        transition: background .1s;
        display: flex;
        align-items: flex-start;
        gap: 9px;
    }
    .place-result:last-child { border-bottom: none; }
    .place-result:hover { background: var(--surface3); }
    .place-result .pr-icon {
        width: 26px; height: 26px;
        background: var(--accent-dim);
        color: var(--accent2);
        border-radius: 5px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 1px;
    }
    .place-result .pr-icon svg { width: 12px; height: 12px; }
    .place-result .pr-name { font-size: .82rem; font-weight: 600; color: var(--text-bright); line-height: 1.2; }
    .place-result .pr-addr { font-size: .72rem; color: var(--text-muted); margin-top: 2px; }
    .place-dropdown .pd-loading,
    .place-dropdown .pd-empty {
        padding: 14px 13px;
        font-size: .78rem;
        color: var(--text-muted);
        text-align: center;
    }

    .autofilled-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: .68rem; font-weight: 600;
        color: var(--green); background: var(--green-dim);
        border: 1px solid rgba(30,201,109,.2);
        padding: 2px 7px; border-radius: 10px;
        margin-left: 6px; vertical-align: middle;
    }

    .map-preview {
        width: 100%;
        height: 180px;
        border-radius: var(--r);
        border: 1px solid var(--border2);
        margin-top: 8px;
        display: none;
        overflow: hidden;
    }
    .map-preview iframe {
        width: 100%; height: 100%; border: none;
    }
    .map-preview.visible { display: block; }

    .coord-row {
        display: flex; gap: 8px; align-items: center;
        font-size: .72rem; color: var(--text-muted);
        margin-top: 4px;
    }
    .coord-row span { font-family: monospace; color: var(--text); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div><h1>Evacuation Shelters</h1><div class="bc">Admin / Shelters</div></div>
    <button class="btn btn-primary" onclick="openModal('modal-add-shelter')">
        <i data-lucide="plus" style="width:14px;height:14px"></i> Add Shelter
    </button>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Shelter</th><th>Location</th><th>Occupancy</th><th>Contact</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($shelters as $s)
            <tr>
                <td style="font-weight:600">{{ $s->name }}</td>
                <td style="font-size:.78rem;color:var(--text-muted)">
                    <i data-lucide="map-pin" style="width:11px;height:11px;display:inline"></i> {{ $s->location }}
                </td>
                <td style="min-width:140px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="flex:1">
                            <div class="progress-bar">
                                <div class="fill {{ $s->getOccupancyPercent() >= 90 ? 'red' : ($s->getOccupancyPercent() >= 60 ? 'yellow' : 'green') }}"
                                     style="width:{{ $s->getOccupancyPercent() }}%"></div>
                            </div>
                        </div>
                        <span style="font-size:.72rem;color:var(--text-muted);white-space:nowrap">{{ $s->current_occupancy }}/{{ $s->capacity }}</span>
                    </div>
                </td>
                <td style="font-size:.78rem">
                    {{ $s->contact_person ?? '—' }}<br>
                    <span style="color:var(--text-muted)">{{ $s->contact_no }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ $s->status === 'open' ? 'completed' : ($s->status === 'full' ? 'high' : 'cancelled') }}">
                        {{ strtoupper($s->status) }}
                    </span>
                </td>
                <td>
                    <button class="btn btn-secondary btn-xs" onclick="openModal('edit-shelter-{{ $s->id }}')">
                        <i data-lucide="pencil" style="width:12px;height:12px"></i> Update
                    </button>
                </td>
            </tr>

            {{-- Edit Shelter Modal --}}
            <div class="modal-backdrop" id="edit-shelter-{{ $s->id }}">
                <div class="modal">
                    <div class="modal-header">
                        <h3>Update: {{ $s->name }}</h3>
                        <button class="modal-close" onclick="closeModal('edit-shelter-{{ $s->id }}')">
                            <i data-lucide="x" style="width:16px;height:16px"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.shelters.update', $s) }}">
                            @csrf @method('PUT')
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Current Occupancy *</label>
                                    <input type="number" name="current_occupancy" value="{{ $s->current_occupancy }}"
                                           class="form-control" min="0" max="{{ $s->capacity }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Status *</label>
                                    <select name="status" class="form-control">
                                        <option value="open"   {{ $s->status=='open'  ?'selected':'' }}>Open</option>
                                        <option value="full"   {{ $s->status=='full'  ?'selected':'' }}>Full</option>
                                        <option value="closed" {{ $s->status=='closed'?'selected':'' }}>Closed</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i data-lucide="save" style="width:14px;height:14px"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i data-lucide="map-pin"></i><p>No shelters found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $shelters->links('vendor.pagination.custom') }}</div>
</div>

{{-- Add Shelter Modal --}}
<div class="modal-backdrop" id="modal-add-shelter">
    <div class="modal" style="max-width:560px">
        <div class="modal-header">
            <h3>Add Evacuation Shelter</h3>
            <button class="modal-close" onclick="closeModal('modal-add-shelter')">
                <i data-lucide="x" style="width:16px;height:16px"></i>
            </button>
        </div>
        <div class="modal-body">

            {{-- Place Search --}}
            <div class="form-group">
                <label>
                    Search Place
                    <span style="font-size:.68rem;color:var(--text-muted);font-weight:400;margin-left:4px">— type to auto-fill fields below</span>
                </label>
                <div class="place-search-wrap" id="place-search-wrap">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" id="place-search-input" class="form-control"
                           placeholder="e.g. Davao City Sports Complex, SPMC..."
                           autocomplete="off">
                    <button type="button" class="clear-btn" id="clear-search" onclick="clearPlaceSearch()">
                        <i data-lucide="x" style="width:12px;height:12px"></i>
                    </button>
                    <div class="place-dropdown" id="place-dropdown"></div>
                </div>
            </div>

            {{-- Map Preview --}}
            <div class="map-preview" id="map-preview">
                <iframe id="map-frame" src="" allowfullscreen loading="lazy"></iframe>
            </div>

            <div style="height:1px;background:var(--border);margin:14px 0 16px"></div>

            <form method="POST" action="{{ route('admin.shelters.store') }}" id="shelter-form">
                @csrf
                <div class="form-group">
                    <label>
                        Shelter Name *
                        <span id="name-autofill-badge" class="autofilled-badge" style="display:none">
                            <i data-lucide="check" style="width:9px;height:9px"></i> Auto-filled
                        </span>
                    </label>
                    <input type="text" name="name" id="shelter-name" class="form-control" required
                           placeholder="Enter shelter name">
                </div>
                <div class="form-group">
                    <label>
                        Address / Location *
                        <span id="addr-autofill-badge" class="autofilled-badge" style="display:none">
                            <i data-lucide="check" style="width:9px;height:9px"></i> Auto-filled
                        </span>
                    </label>
                    <input type="text" name="location" id="shelter-location" class="form-control" required
                           placeholder="Full address">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            Latitude
                            <span id="lat-autofill-badge" class="autofilled-badge" style="display:none">
                                <i data-lucide="check" style="width:9px;height:9px"></i> Auto-filled
                            </span>
                        </label>
                        <input type="number" step="0.0000001" name="latitude" id="shelter-lat"
                               class="form-control" placeholder="e.g. 7.0731690">
                    </div>
                    <div class="form-group">
                        <label>
                            Longitude
                            <span id="lng-autofill-badge" class="autofilled-badge" style="display:none">
                                <i data-lucide="check" style="width:9px;height:9px"></i> Auto-filled
                            </span>
                        </label>
                        <input type="number" step="0.0000001" name="longitude" id="shelter-lng"
                               class="form-control" placeholder="e.g. 125.6128460">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Total Capacity *</label>
                        <input type="number" name="capacity" class="form-control" min="0" required
                               placeholder="0">
                    </div>
                    <div class="form-group">
                        <label>Current Occupancy</label>
                        <input type="number" name="current_occupancy" value="0" class="form-control" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" name="contact_person" class="form-control"
                               placeholder="Full name">
                    </div>
                    <div class="form-group">
                        <label>Contact No.</label>
                        <input type="text" name="contact_no" class="form-control"
                               placeholder="09XXXXXXXXX">
                    </div>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control">
                        <option value="open">Open</option>
                        <option value="full">Full</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="plus" style="width:14px;height:14px"></i> Add Shelter
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal-add-shelter');resetShelterForm()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Store place results in memory to avoid passing complex data through onclick attributes
let placeResults = [];
let searchTimer  = null;

const input    = document.getElementById('place-search-input');
const dropdown = document.getElementById('place-dropdown');
const clearBtn = document.getElementById('clear-search');

// Search as user types
input.addEventListener('input', function () {
    const q = this.value.trim();
    clearBtn.style.display = q ? 'block' : 'none';

    clearTimeout(searchTimer);
    if (q.length < 3) { closeDropdown(); return; }

    dropdown.innerHTML = '<div class="pd-loading"><i data-lucide="loader" style="width:13px;height:13px;display:inline;vertical-align:middle;margin-right:5px"></i> Searching...</div>';
    openDropdown();
    lucide.createIcons();

    searchTimer = setTimeout(() => searchPlaces(q), 450);
});

// Close dropdown when clicking outside
document.addEventListener('click', function (e) {
    if (!document.getElementById('place-search-wrap').contains(e.target)) {
        closeDropdown();
    }
});

async function searchPlaces(query) {
    try {
        const url = `https://nominatim.openstreetmap.org/search?` + new URLSearchParams({
            q:              query + ', Philippines',
            format:         'json',
            limit:          6,
            addressdetails: 1,
            namedetails:    1,
        });

        const res     = await fetch(url, { headers: { 'Accept-Language': 'en', 'User-Agent': 'ERTMS/1.0' } });
        const results = await res.json();

        placeResults = results; // store for later use

        if (!results.length) {
            dropdown.innerHTML = '<div class="pd-empty">No places found. Try a different search.</div>';
            return;
        }

        dropdown.innerHTML = results.map((r, i) => {
            const name    = r.namedetails?.name || r.display_name.split(',')[0];
            const address = r.display_name;
            return `
            <div class="place-result" data-index="${i}">
                <div class="pr-icon"><i data-lucide="map-pin"></i></div>
                <div>
                    <div class="pr-name">${escHtml(name)}</div>
                    <div class="pr-addr">${escHtml(address)}</div>
                </div>
            </div>`;
        }).join('');

        // Attach click listeners using index (safe — no special char issues)
        dropdown.querySelectorAll('.place-result').forEach(el => {
            el.addEventListener('click', function () {
                const idx = parseInt(this.dataset.index);
                fillPlace(placeResults[idx]);
            });
        });

        lucide.createIcons();
    } catch (e) {
        dropdown.innerHTML = '<div class="pd-empty">Search failed. Please fill in manually.</div>';
    }
}

function fillPlace(place) {
    const name    = place.namedetails?.name || place.display_name.split(',')[0];
    const address = place.display_name;
    const lat     = parseFloat(place.lat).toFixed(7);
    const lng     = parseFloat(place.lon).toFixed(7);

    // Fill form fields
    document.getElementById('shelter-name').value     = name;
    document.getElementById('shelter-location').value = address;
    document.getElementById('shelter-lat').value      = lat;
    document.getElementById('shelter-lng').value      = lng;

    // Show auto-filled badges
    ['name', 'addr', 'lat', 'lng'].forEach(id => {
        const badge = document.getElementById(id + '-autofill-badge');
        if (badge) badge.style.display = 'inline-flex';
    });

    // Update search input
    input.value = name;
    clearBtn.style.display = 'block';

    // Show map preview using OpenStreetMap embed
    const mapFrame   = document.getElementById('map-frame');
    const mapPreview = document.getElementById('map-preview');
    mapFrame.src = `https://www.openstreetmap.org/export/embed.html?bbox=${parseFloat(lng)-0.005},${parseFloat(lat)-0.005},${parseFloat(lng)+0.005},${parseFloat(lat)+0.005}&layer=mapnik&marker=${lat},${lng}`;
    mapPreview.classList.add('visible');

    closeDropdown();
    lucide.createIcons();
}

function clearPlaceSearch() {
    input.value            = '';
    clearBtn.style.display = 'none';
    closeDropdown();

    // Hide badges
    ['name', 'addr', 'lat', 'lng'].forEach(id => {
        const badge = document.getElementById(id + '-autofill-badge');
        if (badge) badge.style.display = 'none';
    });

    // Hide map
    document.getElementById('map-preview').classList.remove('visible');
    document.getElementById('map-frame').src = '';
}

function resetShelterForm() {
    document.getElementById('shelter-form').reset();
    clearPlaceSearch();
}

function openDropdown()  { dropdown.classList.add('open'); }
function closeDropdown() { dropdown.classList.remove('open'); }

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
</script>
@endpush