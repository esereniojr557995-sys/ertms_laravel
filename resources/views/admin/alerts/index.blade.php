@extends('layouts.app')
@section('title', 'Alerts')
@section('page-title', 'Alert & Notification System')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.citizen_reports') }}" class="{{ request()->routeIs('admin.citizen_reports*') ? 'active' : '' }}">
    <i data-lucide="file-text"></i> Citizen Reports
</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}" class="active"><i data-lucide="bell"></i> Alerts</a>
<a href="{{ route('admin.comms') }}" class="{{ request()->routeIs('admin.comms*') ? 'active' : '' }}">
    <i data-lucide="message-square"></i> Communications
</a>
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
        <h1>Alerts</h1>
        <div class="bc">Admin / Alerts</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modal-send-alert')">
        <i data-lucide="send" style="width:14px;height:14px"></i> Send Alert
    </button>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Alert</th>
                    <th>Type</th>
                    <th>Severity</th>
                    <th>Sent By</th>
                    <th>Audience</th>
                    <th>Time</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($alerts as $alert)
            <tr>
                <td>
                    <div style="font-weight:600;font-size:.83rem">{{ $alert->title }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        {{ $alert->message }}
                    </div>
                </td>
                <td>
                    <span class="badge badge-info" style="text-transform:capitalize">{{ $alert->type }}</span>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:6px">
                        <div class="alert-dot {{ $alert->severity }}" style="flex-shrink:0"></div>
                        <span class="badge badge-{{ $alert->severity }}">{{ $alert->severity }}</span>
                    </div>
                </td>
                <td style="font-size:.78rem">{{ $alert->sender->name ?? 'Unknown' }}</td>
                
                <!-- FIXED Audience column -->
                <td style="font-size:.72rem;color:var(--text-muted)">
                    @php
                        $audience = $alert->target_audience;
                        if (is_string($audience)) {
                            $audience = explode(',', $audience);
                        }
                        $display = is_array($audience) 
                            ? implode(', ', array_map('trim', array_filter($audience))) 
                            : '—';
                    @endphp
                    {{ $display }}
                </td>

                <td style="font-size:.75rem;color:var(--text-muted)">
                    {{ $alert->created_at->format('M d, H:i') }}
                </td>
                <td>
                    <form id="del-alert-{{ $alert->id }}" method="POST" action="{{ route('admin.alerts.destroy', $alert) }}">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button class="btn btn-danger btn-xs" onclick="confirmDelete('del-alert-{{ $alert->id }}')">
                        <i data-lucide="trash-2" style="width:12px;height:12px"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i data-lucide="bell-off"></i>
                        <p>No alerts sent.</p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">
        {{ $alerts->links('vendor.pagination.custom') }}
    </div>
</div>

{{-- Send Alert Modal --}}
<div class="modal-backdrop" id="modal-send-alert">
    <div class="modal">
        <div class="modal-header">
            <h3>
                <i data-lucide="send" style="width:15px;height:15px;display:inline;vertical-align:middle;margin-right:6px"></i>
                Send New Alert
            </h3>
            <button class="modal-close" onclick="closeModal('modal-send-alert')">
                <i data-lucide="x" style="width:16px;height:16px"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.alerts.store') }}">
                @csrf
                <div class="form-group">
                    <label>Alert Title *</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Evacuation Order — Matina">
                </div>
                
                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" class="form-control" rows="3" required placeholder="Full alert message…"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Severity *</label>
                        <select name="severity" class="form-control" required>
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Type *</label>
                        <select name="type" class="form-control" required>
                            <option value="evacuation">Evacuation</option>
                            <option value="weather">Weather</option>
                            <option value="incident">Incident</option>
                            <option value="public">Public</option>
                            <option value="system">System</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Target Audience *</label>
                    <div style="display:flex;flex-direction:column;gap:8px;margin-top:4px">
                        <div class="form-check">
                            <input type="checkbox" name="target_audience[]" value="all" id="aud-all">
                            <label for="aud-all">All Users</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="target_audience[]" value="responders" id="aud-resp">
                            <label for="aud-resp">Responders</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="target_audience[]" value="citizens" id="aud-cit">
                            <label for="aud-cit">Citizens</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Link to Incident</label>
                    <select name="incident_id" class="form-control">
                        <option value="">— None —</option>
                        @foreach($incidents as $inc)
                            <option value="{{ $inc->id }}">{{ $inc->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer" style="padding:0;margin-top:16px;border:none">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal-send-alert')">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="send" style="width:14px;height:14px"></i> Send Alert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection