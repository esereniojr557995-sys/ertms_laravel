@extends('layouts.app')
@section('title','Report Incident')
@section('page-title','Public Incident Portal')
@section('sidebar-nav')@include('citizen._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>Report an Incident</h1><div class="bc">Citizen / Portal</div></div>
</div>

<div class="grid-2" style="align-items:start">

    {{-- Submit Form --}}
    <div class="card">
        <div class="card-header"><h2><i data-lucide="file-plus"></i> Submit New Report</h2></div>
        <div class="card-body">
            <div class="info-box blue" style="margin-bottom:18px">
                <i data-lucide="info"></i>
                <div>Your report will be reviewed by our emergency response team. For life-threatening emergencies, call <strong style="color:var(--red)">911</strong> immediately.</div>
            </div>

            <form method="POST" action="{{ route('citizen.portal.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Incident Type first so title suggestions work --}}
                <div class="form-group">
                    <label>Incident Type *</label>
                    <select name="type" id="incident-type" class="form-control" required
                            onchange="suggestTitles(this.value)" style="font-size:1rem">
                        <option value="">— Select Type —</option>
                        @foreach(['fire','flood','accident','medical','hazard','other'] as $t)
                        <option value="{{ $t }}" {{ old('type')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Title with suggestions --}}
                <div class="form-group">
                    <label>
                        Incident Title *
                        <span style="font-size:.68rem;color:var(--text-muted);font-weight:400;margin-left:4px">— or pick a suggestion below</span>
                    </label>
                    <input type="text" name="title" id="incident-title"
                           value="{{ old('title') }}" class="form-control" required
                           placeholder="Brief description of what you observed"
                           style="font-size:1rem">
                    {{-- Suggested titles --}}
                    <div id="title-suggestions" style="display:none;margin-top:6px;display:flex;gap:6px;flex-wrap:wrap"></div>
                </div>

                {{-- Location (optional) --}}
                <div class="form-group">
                    <label>
                        Location
                        <span style="font-size:.68rem;color:var(--text-muted);font-weight:400;margin-left:4px">(optional — GPS below can fill this)</span>
                    </label>
                    <input type="text" name="location" value="{{ old('location') }}" class="form-control"
                           placeholder="Street, Barangay, City" style="font-size:1rem">
                </div>

                {{-- Hidden lat/lng --}}
                <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                {{-- GPS Location --}}
                <div class="form-group">
                    <label>GPS Coordinates <span style="color:var(--text-muted)">(optional)</span></label>
                    <button type="button" id="locate-btn" class="btn btn-secondary"
                            onclick="getLocation()"
                            style="width:100%;justify-content:center;padding:12px;font-size:.9rem">
                        <i data-lucide="map-pin" style="width:15px;height:15px"></i>
                        Use My Location
                    </button>
                    <div id="location-preview" style="display:none;margin-top:8px;padding:10px 12px;background:var(--surface2);border:1px solid var(--border2);border-radius:var(--r-sm);font-size:.82rem;font-family:var(--font-mono);color:var(--green)"></div>
                    <div id="location-status" style="font-size:.78rem;color:var(--text-muted);margin-top:5px;text-align:center"></div>
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" class="form-control" rows="4" required
                              placeholder="Describe what you saw — time, number of people affected, any other details."
                              style="font-size:1rem">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Photo <span style="color:var(--text-muted)">(optional, max 2MB)</span></label>
                    <input type="file" name="photo" class="form-control" accept="image/*" capture="environment">
                </div>

                <button type="submit" class="btn btn-primary"
                        style="width:100%;justify-content:center;padding:13px;font-size:1rem">
                    <i data-lucide="send" style="width:16px;height:16px"></i> Submit Report
                </button>
            </form>
        </div>
    </div>

    {{-- My Reports --}}
    <div class="card">
        <div class="card-header"><h2><i data-lucide="clipboard-list"></i> My Submitted Reports</h2></div>
        @if($myReports->isEmpty())
        <div class="empty-state" style="padding:36px">
            <i data-lucide="file-text"></i>
            <p>No reports submitted yet.</p>
        </div>
        @else
        <div>
            @foreach($myReports as $report)
            @php $isPending = $report->status === 'pending'; @endphp
            <div style="padding:14px 16px;border-bottom:1px solid rgba(23,32,48,.8)">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:600;font-size:.84rem;color:var(--text-bright)">{{ $report->title }}</div>
                        @if($report->location)
                        <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            <i data-lucide="map-pin" style="width:10px;height:10px;display:inline;vertical-align:middle"></i>
                            {{ $report->location }}
                        </div>
                        @endif
                        <div style="display:flex;gap:6px;margin-top:7px;flex-wrap:wrap;align-items:center">
                            <span class="badge badge-info">{{ $report->type }}</span>
                            <span class="badge badge-{{ $report->status==='acknowledged'?'in_progress':($report->status==='resolved'?'completed':($report->status==='dismissed'?'cancelled':'pending')) }}">
                                {{ $report->status }}
                            </span>
                            <span style="font-size:.7rem;color:var(--text-muted);margin-left:auto">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div style="display:flex;gap:5px;flex-shrink:0">
                        <a href="{{ route('citizen.portal.show', $report) }}" class="btn btn-secondary btn-xs" style="padding:7px 10px">
                            <i data-lucide="eye" style="width:13px;height:13px"></i>
                        </a>
                        {{-- Cancel button: only for pending reports --}}
                        @if($isPending)
                        <button class="btn btn-danger btn-xs" style="padding:7px 10px"
                                onclick="openModal('cancel-{{ $report->id }}')">
                            <i data-lucide="x" style="width:13px;height:13px"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cancel Confirmation Modal --}}
            @if($isPending)
            <div class="modal-backdrop" id="cancel-{{ $report->id }}">
                <div class="modal" style="max-width:380px">
                    <div class="modal-header">
                        <h3>Cancel Report</h3>
                        <button class="modal-close" onclick="closeModal('cancel-{{ $report->id }}')">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="info-box yellow" style="margin-bottom:14px">
                            <i data-lucide="alert-triangle"></i>
                            <div>Are you sure you want to cancel <strong>{{ $report->title }}</strong>? This cannot be undone.</div>
                        </div>
                        <form method="POST" action="{{ route('citizen.portal.cancel', $report) }}">
                            @csrf @method('PUT')
                            <div style="display:flex;gap:8px">
                                <button type="button" class="btn btn-secondary" style="flex:1"
                                        onclick="closeModal('cancel-{{ $report->id }}')">Keep It</button>
                                <button type="submit" class="btn btn-danger" style="flex:1">
                                    <i data-lucide="x" style="width:13px;height:13px"></i> Cancel Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @endforeach
        </div>
        <div style="padding:12px 16px">{{ $myReports->links('vendor.pagination.custom') }}</div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Pre-generated title suggestions per type ──────────────────────────────
const titleSuggestions = {
    fire:     ['House fire reported', 'Building fire in progress', 'Grass fire spreading', 'Vehicle on fire'],
    flood:    ['Road flooded — cannot pass', 'Flash flood in barangay', 'Flooded homes in area', 'Rising water levels'],
    accident: ['Road accident with injuries', 'Multi-vehicle collision', 'Motorcycle accident', 'Pedestrian hit by vehicle'],
    medical:  ['Person collapsed — needs help', 'Medical emergency — unconscious victim', 'Injured person on road', 'Suspected heart attack'],
    hazard:   ['Fallen tree blocking road', 'Downed power line — danger', 'Gas leak reported', 'Landslide blocking road'],
    other:    ['Emergency situation', 'Suspicious activity', 'Infrastructure damage', 'Needs immediate assistance'],
};

function suggestTitles(type) {
    const container = document.getElementById('title-suggestions');
    const suggestions = titleSuggestions[type];
    if (!suggestions) { container.style.display = 'none'; container.innerHTML = ''; return; }

    container.style.display = 'flex';
    container.innerHTML = suggestions.map(s =>
        `<button type="button" onclick="useTitle('${s}')"
            style="padding:4px 10px;border-radius:20px;font-size:.75rem;background:var(--surface3);border:1px solid var(--border2);color:var(--text);cursor:pointer;transition:all .1s;white-space:nowrap"
            onmouseover="this.style.background='var(--surface4)'"
            onmouseout="this.style.background='var(--surface3)'">${s}</button>`
    ).join('');
}

function useTitle(title) {
    document.getElementById('incident-title').value = title;
    // Scroll to title field
    document.getElementById('incident-title').focus();
}

// ── GPS Location ──────────────────────────────────────────────────────────
function getLocation() {
    const btn     = document.getElementById('locate-btn');
    const status  = document.getElementById('location-status');
    const preview = document.getElementById('location-preview');

    if (!navigator.geolocation) {
        status.style.color = 'var(--red)';
        status.textContent = 'Geolocation not supported by your browser.';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader" style="width:15px;height:15px"></i> Getting location...';
    status.style.color = 'var(--text-muted)';
    status.textContent = 'Please allow location access when prompted…';
    lucide.createIcons();

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude.toFixed(7);
            const lng = position.coords.longitude.toFixed(7);
            document.getElementById('latitude').value  = lat;
            document.getElementById('longitude').value = lng;
            status.style.color = 'var(--green)';
            status.textContent = '✓ Location captured!';
            preview.style.display = 'block';
            preview.innerHTML = '📍 ' + lat + ', ' + lng;
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="map-pin" style="width:15px;height:15px"></i> Update Location';
            lucide.createIcons();
        },
        function(error) {
            const messages = { 1:'Location access denied. Please allow in browser settings.', 2:'Location unavailable. Try again.', 3:'Request timed out. Try again.' };
            status.style.color = 'var(--red)';
            status.textContent = messages[error.code] || 'Could not get location.';
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="map-pin" style="width:15px;height:15px"></i> Use My Location';
            lucide.createIcons();
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

// Pre-fill suggestions if type already selected (e.g. after validation error)
const savedType = document.getElementById('incident-type').value;
if (savedType) suggestTitles(savedType);
</script>
@endpush