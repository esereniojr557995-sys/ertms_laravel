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
        <div class="card-header"><h2><i data-lucide="file-plus" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Submit New Report</h2></div>
        <div class="card-body">
            <div style="background:rgba(56,139,253,.08);border:1px solid rgba(56,139,253,.2);border-radius:6px;padding:10px 14px;margin-bottom:18px;font-size:.8rem;color:var(--text-muted)">
                <i data-lucide="info" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:4px"></i>
                Your report will be reviewed by our emergency response team. For life-threatening emergencies, call <strong style="color:var(--red)">911</strong> immediately.
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
                <div class="form-row">
                    <div class="form-group">
                        <label>Latitude <span style="color:var(--text-muted)">(optional)</span></label>
                        <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" class="form-control" placeholder="7.0731">
                    </div>
                    <div class="form-group">
                        <label>Longitude <span style="color:var(--text-muted)">(optional)</span></label>
                        <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" class="form-control" placeholder="125.6128">
                    </div>
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
        <div class="card-header"><h2><i data-lucide="clipboard-list" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>My Submitted Reports</h2></div>
        @if($myReports->isEmpty())
        <div class="empty-state" style="padding:36px"><i data-lucide="file-text"></i><p>No reports submitted yet.</p></div>
        @else
        <div style="padding:0">
            @foreach($myReports as $report)
            <div style="padding:14px 18px;border-bottom:1px solid rgba(48,54,61,.5)">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                    <div style="flex:1">
                        <div style="font-weight:600;font-size:.83rem">{{ $report->title }}</div>
                        <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px">
                            <i data-lucide="map-pin" style="width:10px;height:10px;display:inline"></i> {{ $report->location }}
                        </div>
                        <div style="display:flex;gap:6px;margin-top:6px;flex-wrap:wrap">
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
