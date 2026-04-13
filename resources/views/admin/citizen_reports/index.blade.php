@extends('layouts.app')
@section('title','Citizen Reports')
@section('page-title','Citizen Reports')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.citizen_reports') }}" class="active"><i data-lucide="file-text"></i> Citizen Reports</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}"><i data-lucide="bell"></i> Alerts</a>
<a href="{{ route('admin.patients') }}"><i data-lucide="heart-pulse"></i> Medical</a>
<a href="{{ route('admin.training') }}"><i data-lucide="graduation-cap"></i> Training</a>
<a href="{{ route('admin.shelters') }}"><i data-lucide="map-pin"></i> Shelters</a>
<div class="sb-section">Management</div>
<a href="{{ route('admin.users') }}"><i data-lucide="users"></i> Users</a>
<a href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-2"></i> Reports</a>
<a href="{{ route('admin.audit_logs') }}"><i data-lucide="scroll-text"></i> Audit Logs</a>
<a href="{{ route('admin.settings') }}"><i data-lucide="settings"></i> Settings</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Citizen Reports</h1>
        <div class="bc">Admin / Citizen Reports</div>
    </div>
</div>

{{-- Summary Stat Cards --}}
@php
    $allReports   = $reports->getCollection();
    $statusCounts = $allReports->groupBy('status');
    $statusConfig = [
        'pending'      => ['color' => 'yellow', 'icon' => 'clock',        'label' => 'Pending'],
        'acknowledged' => ['color' => 'blue',   'icon' => 'eye',          'label' => 'Acknowledged'],
        'resolved'     => ['color' => 'green',  'icon' => 'check-circle', 'label' => 'Resolved'],
        'dismissed'    => ['color' => '',       'icon' => 'x-circle',     'label' => 'Dismissed'],
    ];
@endphp
<div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:22px">
    @foreach($statusConfig as $status => $cfg)
    <div class="stat-card {{ $cfg['color'] }}">
        <div class="sc-icon"><i data-lucide="{{ $cfg['icon'] }}" style="width:14px;height:14px"></i></div>
        <div class="sc-val">{{ $statusCounts->get($status)?->count() ?? 0 }}</div>
        <div class="sc-label">{{ $cfg['label'] }}</div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="margin:0">
            <select name="status" class="form-control">
                <option value="">All Status</option>
                @foreach(['pending','acknowledged','resolved','dismissed'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="type" class="form-control">
                <option value="">All Types</option>
                @foreach(['fire','flood','accident','medical','hazard','other'] as $t)
                <option value="{{ $t }}" {{ request('type')===$t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit">
                <i data-lucide="filter" style="width:13px;height:13px"></i> Filter
            </button>
        </form>
        <span style="color:var(--text-muted);font-size:.78rem">{{ $reports->total() }} reports</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Report</th>
                    <th>Type</th>
                    <th>Submitted By</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($reports as $report)
            <tr>
                <td>
                    <div style="font-weight:600;font-size:.83rem;color:var(--text)">{{ $report->title }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        {{ $report->description }}
                    </div>
                </td>
                <td>
                    <span class="badge badge-info" style="text-transform:capitalize">{{ $report->type }}</span>
                </td>
                <td>
                    <div style="font-size:.82rem;font-weight:500;color:var(--text)">{{ $report->user?->name ?? '—' }}</div>
                    <div style="font-size:.7rem;color:var(--text-muted)">{{ $report->user?->email }}</div>
                </td>
                <td style="font-size:.78rem;color:var(--text-muted);max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    {{ $report->location }}
                </td>
                <td>
                    @php
                        $badgeMap = ['pending'=>'pending','acknowledged'=>'in_progress','resolved'=>'completed','dismissed'=>'cancelled'];
                    @endphp
                    <span class="badge badge-{{ $badgeMap[$report->status] ?? 'pending' }}">{{ $report->status }}</span>
                </td>
                <td style="font-size:.75rem;color:var(--text-muted);white-space:nowrap">
                    {{ $report->created_at->diffForHumans() }}
                </td>
                <td>
                    <div style="display:flex;gap:5px;flex-wrap:wrap">
                        <button class="btn btn-secondary btn-xs" onclick="openModal('view-{{ $report->id }}')">
                            <i data-lucide="eye" style="width:12px;height:12px"></i> View
                        </button>
                        <button class="btn btn-secondary btn-xs" onclick="openModal('status-{{ $report->id }}')">
                            <i data-lucide="pencil" style="width:12px;height:12px"></i> Status
                        </button>
                        @if(in_array($report->status, ['pending','acknowledged']))
                        <button class="btn btn-primary btn-xs" onclick="openModal('escalate-{{ $report->id }}')">
                            <i data-lucide="flame" style="width:12px;height:12px"></i> Escalate
                        </button>
                        @endif
                    </div>
                </td>
            </tr>

            {{-- ── View Modal ── --}}
            <div class="modal-backdrop" id="view-{{ $report->id }}">
                <div class="modal" style="max-width:560px">
                    <div class="modal-header">
                        <h3>{{ $report->title }}</h3>
                        <button class="modal-close" onclick="closeModal('view-{{ $report->id }}')">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="detail-row">
                            <span class="dl">Submitted By</span>
                            <span class="dv">{{ $report->user?->name ?? '—' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="dl">Type</span>
                            <span class="dv" style="text-transform:capitalize">{{ $report->type }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="dl">Location</span>
                            <span class="dv">{{ $report->location }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="dl">Status</span>
                            <span class="dv">
                                <span class="badge badge-{{ $badgeMap[$report->status] ?? 'pending' }}">{{ $report->status }}</span>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="dl">Submitted</span>
                            <span class="dv">{{ $report->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($report->latitude && $report->longitude)
                        <div class="detail-row">
                            <span class="dl">Coordinates</span>
                            <span class="dv" style="font-family:monospace;font-size:.78rem">{{ $report->latitude }}, {{ $report->longitude }}</span>
                        </div>
                        @endif
                        <div style="margin-top:16px">
                            <div style="font-size:.67rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.09em;margin-bottom:7px">Description</div>
                            <div style="font-size:.83rem;color:var(--text);line-height:1.65;background:var(--surface2);padding:12px 14px;border-radius:var(--radius-sm);border:1px solid var(--border)">
                                {{ $report->description }}
                            </div>
                        </div>
                        @if($report->photo)
                        <div style="margin-top:16px">
                            <div style="font-size:.67rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.09em;margin-bottom:7px">Attached Photo</div>
                            <img src="{{ asset('storage/'.$report->photo) }}" alt="Report photo"
                                 style="max-width:100%;border-radius:var(--radius-sm);border:1px solid var(--border)">
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Update Status Modal ── --}}
            <div class="modal-backdrop" id="status-{{ $report->id }}">
                <div class="modal" style="max-width:380px">
                    <div class="modal-header">
                        <h3>Update Status</h3>
                        <button class="modal-close" onclick="closeModal('status-{{ $report->id }}')">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.citizen_reports.update_status', $report) }}">
                            @csrf @method('PUT')
                            <div class="form-group">
                                <label>Report Status</label>
                                <select name="status" class="form-control">
                                    @foreach(['pending','acknowledged','resolved','dismissed'] as $s)
                                    <option value="{{ $s }}" {{ $report->status===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="display:flex;gap:8px">
                                <button type="submit" class="btn btn-primary">
                                    <i data-lucide="save" style="width:13px;height:13px"></i> Save
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="closeModal('status-{{ $report->id }}')">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ── Escalate to Incident Modal ── --}}
            @if(in_array($report->status, ['pending','acknowledged']))
            <div class="modal-backdrop" id="escalate-{{ $report->id }}">
                <div class="modal" style="max-width:420px">
                    <div class="modal-header">
                        <h3>Escalate to Incident</h3>
                        <button class="modal-close" onclick="closeModal('escalate-{{ $report->id }}')">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="info-box blue">
                            <i data-lucide="info" style="width:14px;height:14px;flex-shrink:0"></i>
                            <div>This will create a new incident from <strong style="color:var(--text)">{{ $report->title }}</strong> and mark the report as acknowledged.</div>
                        </div>
                        <form method="POST" action="{{ route('admin.citizen_reports.escalate', $report) }}">
                            @csrf
                            <div class="form-group">
                                <label>Severity *</label>
                                <select name="severity" class="form-control" required>
                                    @foreach(['low','moderate','high','critical'] as $sv)
                                    <option value="{{ $sv }}">{{ ucfirst($sv) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Assign Commander</label>
                                <select name="commander_id" class="form-control">
                                    <option value="">— None —</option>
                                    @foreach(\App\Models\User::where('role','commander')->get() as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="display:flex;gap:8px">
                                <button type="submit" class="btn btn-primary">
                                    <i data-lucide="flame" style="width:13px;height:13px"></i> Create Incident
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="closeModal('escalate-{{ $report->id }}')">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i data-lucide="file-text"></i>
                        <p>No citizen reports found.</p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $reports->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>
@endsection