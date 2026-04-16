@extends('layouts.app')
@section('title','Report Incident')
@section('page-title','Public Incident Portal')
@section('sidebar-nav')@include('citizen._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>Report an Incident</h1><div class="bc">Citizen / Portal</div></div>
</div>

{{-- On mobile: form first, reports below. On desktop: side by side --}}
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

                <div class="form-group">
                    <label>Incident Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                           required placeholder="Brief description of what you observed"
                           style="font-size:1rem"> {{-- larger on mobile --}}
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Incident Type *</label>
                        <select name="type" class="form-control" required style="font-size:1rem">
                            <option value="">— Select Type —</option>
                            @foreach(['fire','flood','accident','medical','hazard','other'] as $t)
                            <option value="{{ $t }}" {{ old('type')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" name="location" value="{{ old('location') }}" class="form-control"
                               required placeholder="Street, Barangay, City" style="font-size:1rem">
                    </div>
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
                    {{-- capture="environment" opens camera directly on phones --}}
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
            <div style="padding:14px 16px;border-bottom:1px solid rgba(23,32,48,.8)">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:600;font-size:.84rem;color:var(--text-bright)">{{ $report->title }}</div>
                        <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            <i data-lucide="map-pin" style="width:10px;height:10px;display:inline;vertical-align:middle"></i>
                            {{ $report->location }}
                        </div>
                        <div style="display:flex;gap:6px;margin-top:7px;flex-wrap:wrap;align-items:center">
                            <span class="badge badge-info">{{ $report->type }}</span>
                            <span class="badge badge-{{ $report->status==='acknowledged'?'in_progress':($report->status==='resolved'?'completed':($report->status==='dismissed'?'cancelled':'pending')) }}">
                                {{ $report->status }}
                            </span>
                            <span style="font-size:.7rem;color:var(--text-muted);margin-left:auto">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('citizen.portal.show', $report) }}" class="btn btn-secondary btn-xs" style="flex-shrink:0;padding:7px 10px">
                        <i data-lucide="eye" style="width:13px;height:13px"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div style="padding:12px 16px">{{ $myReports->links('vendor.pagination.custom') }}</div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
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
            const messages = {
                1: 'Location access denied. Please allow location in your browser settings.',
                2: 'Location unavailable. Please try again.',
                3: 'Request timed out. Please try again.',
            };
            status.style.color = 'var(--red)';
            status.textContent = messages[error.code] || 'Could not get location.';
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="map-pin" style="width:15px;height:15px"></i> Use My Location';
            lucide.createIcons();
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}
</script>
@endpush