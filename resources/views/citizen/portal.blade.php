@extends('layouts.app')
@section('title','Report Incident')
@section('page-title','Public Incident Portal')
@section('sidebar-nav')@include('citizen._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>Report an Incident</h1><div class="breadcrumb">Citizen / Portal</div></div>
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

                <div class="form-group">
                    <label>Incident Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control" required placeholder="Brief description of what you observed">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Incident Type *</label>
                        <select name="type" class="form-control" required>
                            <option value="">— Select Type —</option>
                            @foreach(['fire','flood','accident','medical','hazard','other'] as $t)
                            <option value="{{ $t }}" {{ old('type')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" name="location" value="{{ old('location') }}" class="form-control" required placeholder="Street, Barangay, City">
                    </div>
                </div>

                {{-- Hidden lat/lng fields --}}
                <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                {{-- Use My Location Button --}}
                <div class="form-group">
                    <label>GPS Coordinates <span style="color:var(--text-muted)">(optional)</span></label>
                    <div style="display:flex;align-items:center;gap:10px">
                        <button type="button" id="locate-btn" class="btn btn-secondary" onclick="getLocation()">
                            <i data-lucide="map-pin" style="width:13px;height:13px"></i> Use My Location
                        </button>
                        <span id="location-status" style="font-size:.78rem;color:var(--text-muted)">No location set</span>
                    </div>
                    <div id="location-preview" style="display:none;margin-top:8px;padding:8px 12px;background:var(--surface2);border:1px solid var(--border2);border-radius:var(--r-sm);font-size:.78rem;font-family:var(--font-mono);color:var(--green)"></div>
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" class="form-control" rows="4" required placeholder="Describe what you saw in detail. Include time, number of people affected, and any other relevant information.">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Photo <span style="color:var(--text-muted)">(optional, max 2MB)</span></label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i data-lucide="send" style="width:14px;height:14px"></i> Submit Report
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
            <div style="padding:14px 18px;border-bottom:1px solid rgba(48,54,61,.5)">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                    <div style="flex:1">
                        <div style="font-weight:600;font-size:.83rem;color:var(--text-bright)">{{ $report->title }}</div>
                        <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px">
                            <i data-lucide="map-pin" style="width:10px;height:10px;display:inline"></i> {{ $report->location }}
                        </div>
                        <div style="display:flex;gap:6px;margin-top:6px;flex-wrap:wrap;align-items:center">
                            <span class="badge badge-info">{{ $report->type }}</span>
                            <span class="badge badge-{{ $report->status==='acknowledged'?'in_progress':($report->status==='resolved'?'completed':($report->status==='dismissed'?'cancelled':'pending')) }}">
                                {{ $report->status }}
                            </span>
                            <span style="font-size:.7rem;color:var(--text-muted)">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('citizen.portal.show', $report) }}" class="btn btn-secondary btn-xs" style="flex-shrink:0">
                        <i data-lucide="eye" style="width:12px;height:12px"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div style="padding:12px 18px">{{ $myReports->links('vendor.pagination.custom') }}</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function getLocation() {
    const btn    = document.getElementById('locate-btn');
    const status = document.getElementById('location-status');
    const preview = document.getElementById('location-preview');

    if (!navigator.geolocation) {
        status.style.color = 'var(--red)';
        status.textContent = 'Geolocation is not supported by your browser.';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader" style="width:13px;height:13px"></i> Locating...';
    status.style.color = 'var(--text-muted)';
    status.textContent = 'Getting your location...';
    lucide.createIcons();

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude.toFixed(7);
            const lng = position.coords.longitude.toFixed(7);

            document.getElementById('latitude').value  = lat;
            document.getElementById('longitude').value = lng;

            status.style.color = 'var(--green)';
            status.textContent = 'Location captured!';

            preview.style.display = 'block';
            preview.textContent = '📍 ' + lat + ', ' + lng;

            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="map-pin" style="width:13px;height:13px"></i> Update Location';
            lucide.createIcons();
        },
        function(error) {
            const messages = {
                1: 'Location access denied. Please allow location in your browser.',
                2: 'Location unavailable. Try again.',
                3: 'Request timed out. Try again.',
            };
            status.style.color = 'var(--red)';
            status.textContent = messages[error.code] || 'Could not get location.';

            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="map-pin" style="width:13px;height:13px"></i> Use My Location';
            lucide.createIcons();
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}
</script>
@endpush