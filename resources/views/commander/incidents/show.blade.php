@extends('layouts.app')
@section('title', $incident->title)
@section('page-title','Incident Detail')
@section('sidebar-nav')@include('commander._nav')@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar {
        background: var(--surface2) !important;
        border: 1px solid var(--border2) !important;
        border-radius: var(--r) !important;
        box-shadow: 0 16px 40px rgba(0,0,0,.5) !important;
        font-family: var(--font) !important;
    }
    .flatpickr-day {
        color: var(--text) !important;
        border-radius: var(--r-sm) !important;
    }
    .flatpickr-day:hover { background: var(--surface3) !important; }
    .flatpickr-day.selected {
        background: var(--accent) !important;
        border-color: var(--accent) !important;
        color: #fff !important;
    }
    .flatpickr-day.today {
        border-color: var(--accent) !important;
        color: var(--accent2) !important;
    }
    .flatpickr-day.flatpickr-disabled { color: var(--text-dim) !important; }
    .flatpickr-months { background: var(--surface3) !important; border-radius: var(--r) var(--r) 0 0 !important; }
    .flatpickr-month, .flatpickr-current-month, .flatpickr-monthDropdown-months,
    .flatpickr-current-month input.cur-year {
        color: var(--text-bright) !important;
        fill: var(--text-bright) !important;
        background: transparent !important;
    }
    .flatpickr-weekday { color: var(--text-muted) !important; background: transparent !important; }
    .flatpickr-weekdays { background: var(--surface3) !important; }
    .flatpickr-time { background: var(--surface3) !important; border-top: 1px solid var(--border) !important; border-radius: 0 0 var(--r) var(--r) !important; }
    .flatpickr-time input, .flatpickr-time .flatpickr-am-pm {
        color: var(--text-bright) !important;
        background: transparent !important;
    }
    .flatpickr-time input:hover, .flatpickr-time .flatpickr-am-pm:hover { background: var(--surface4) !important; }
    .numInputWrapper span { border-color: var(--border2) !important; }
    .numInputWrapper span svg path { fill: var(--text-muted) !important; }
    .flatpickr-prev-month svg, .flatpickr-next-month svg { fill: var(--text-muted) !important; }
    .flatpickr-prev-month:hover svg, .flatpickr-next-month:hover svg { fill: var(--text-bright) !important; }

    .date-input-wrap {
        position: relative;
    }
    .date-input-wrap .cal-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
        width: 14px; height: 14px;
    }
    .date-input-wrap .form-control {
        padding-right: 32px;
        cursor: pointer;
    }
    .date-input-wrap .form-control:read-only { cursor: pointer; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $incident->title }}</h1>
        <div class="bc">Commander / Incidents / Detail</div>
    </div>
    <a href="{{ route('commander.incidents') }}" class="btn btn-secondary"><i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back</a>
</div>

<div class="grid-2" style="margin-bottom:20px;align-items:start">
    {{-- Incident Info --}}
    <div class="card">
        <div class="card-header"><h2>Incident Information</h2></div>
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                <div class="inc-icon {{ $incident->type }}" style="width:44px;height:44px">
                    <i data-lucide="{{ $incident->getTypeIcon() }}" style="width:20px;height:20px"></i>
                </div>
                <div>
                    <span class="badge badge-{{ $incident->severity }}" style="margin-right:6px">{{ $incident->severity }}</span>
                    <span class="badge badge-{{ $incident->status }}">{{ $incident->status }}</span>
                </div>
            </div>
            @foreach([
                ['Location', $incident->location],
                ['Type', ucfirst($incident->type)],
                ['Reported By', $incident->reporter->name],
                ['Commander', $incident->commander?->name ?? 'Unassigned'],
                ['Reported', $incident->date_reported->format('M d, Y H:i')],
                ['Closed', $incident->date_closed?->format('M d, Y H:i') ?? '—'],
            ] as [$label,$value])
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(48,54,61,.5);font-size:.82rem">
                <span style="color:var(--text-muted)">{{ $label }}</span>
                <span style="font-weight:500">{{ $value }}</span>
            </div>
            @endforeach
            @if($incident->description)
            <div style="margin-top:12px">
                <div style="font-size:.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Description</div>
                <div style="font-size:.82rem;line-height:1.6;color:var(--text)">{{ $incident->description }}</div>
            </div>
            @endif
        </div>
        <div class="card-header" style="border-top:1px solid var(--border);border-bottom:none"><h2>Update Status</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('commander.incidents.update', $incident) }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
                @csrf @method('PUT')
                <div class="form-group" style="flex:1;margin:0">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        @foreach(['open','active','contained','closed'] as $s)
                        <option value="{{ $s }}" {{ $incident->status==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex:1;margin:0">
                    <label>Severity</label>
                    <select name="severity" class="form-control">
                        @foreach(['low','moderate','high','critical'] as $s)
                        <option value="{{ $s }}" {{ $incident->severity==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-bottom:0"><i data-lucide="save" style="width:14px;height:14px"></i> Update</button>
            </form>
        </div>
    </div>

    {{-- Add Task --}}
    <div class="card">
        <div class="card-header"><h2>Assign Task</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('commander.tasks.store') }}">
                @csrf
                <input type="hidden" name="incident_id" value="{{ $incident->id }}">
                <div class="form-group"><label>Task Title *</label><input type="text" name="title" class="form-control" required placeholder="e.g. Set up command post"></div>
                <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Priority *</label>
                        <select name="priority" class="form-control">
                            @foreach(['low','medium','high','critical'] as $p)
                            <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Assign To</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">— Unassigned —</option>
                            @foreach($responders as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Due Date & Time</label>
                    <div class="date-input-wrap">
                        <input type="text" name="due_datetime" id="due_datetime_show"
                               class="form-control" placeholder="Select date and time…" readonly>
                        <input type="hidden" name="due_datetime" id="due_datetime_val">
                        <i data-lucide="calendar" class="cal-icon"></i>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i data-lucide="plus" style="width:14px;height:14px"></i> Assign Task</button>
            </form>
        </div>
    </div>
</div>

{{-- Tasks list --}}
<div class="card">
    <div class="card-header"><h2><i data-lucide="check-square" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Tasks for this Incident</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Task</th><th>Priority</th><th>Assigned To</th><th>Status</th><th>Due</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($incident->tasks as $task)
            @php $isLocked = $task->status === 'completed'; @endphp
            <tr style="{{ $isLocked ? 'opacity:.65' : '' }}">
                <td>
                    <div style="font-weight:600;font-size:.83rem;display:flex;align-items:center;gap:7px">
                        {{ $task->title }}
                        @if($isLocked)
                        <span style="display:inline-flex;align-items:center;gap:3px;font-size:.62rem;font-weight:600;color:var(--green);background:var(--green-dim);border:1px solid rgba(30,201,109,.2);padding:1px 6px;border-radius:10px">
                            <i data-lucide="lock" style="width:9px;height:9px"></i> LOCKED
                        </span>
                        @endif
                    </div>
                    @if($task->description)<div style="font-size:.72rem;color:var(--text-muted)">{{ Str::limit($task->description,60) }}</div>@endif
                </td>
                <td><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                <td style="font-size:.82rem">{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                <td><span class="badge badge-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span></td>
                <td style="font-size:.75rem;color:{{ $task->due_datetime && $task->due_datetime->isPast() && !$isLocked ? 'var(--red)' : 'var(--text-muted)' }}">
                    {{ $task->due_datetime?->format('M d, H:i') ?? '—' }}
                </td>
                <td>
                    @if($isLocked)
                        <span style="font-size:.74rem;color:var(--text-dim);display:flex;align-items:center;gap:4px">
                            <i data-lucide="check-circle" style="width:13px;height:13px;color:var(--green)"></i> Completed
                        </span>
                    @else
                        <div style="display:flex;align-items:center;gap:6px">
                            <form method="POST" action="{{ route('commander.tasks.update', $task) }}" style="display:inline">
                                @csrf @method('PUT')
                                <select name="status" class="form-control" style="width:auto;padding:4px 8px;font-size:.75rem;display:inline" onchange="this.form.submit()">
                                    @foreach(['pending','in_progress','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $task->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                    @endforeach
                                </select>
                            </form>
                            <form id="del-task-{{ $task->id }}" method="POST" action="{{ route('commander.tasks.destroy', $task) }}">@csrf @method('DELETE')</form>
                            <button class="btn btn-danger btn-xs" onclick="confirmDelete('del-task-{{ $task->id }}')"><i data-lucide="trash-2" style="width:12px;height:12px"></i></button>
                        </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state" style="padding:20px"><p>No tasks assigned.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Patients --}}
@if($incident->patients->count() > 0)
<div class="card" style="margin-top:20px">
    <div class="card-header"><h2><i data-lucide="heart-pulse" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Patients ({{ $incident->patients->count() }})</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Patient</th><th>Triage</th><th>Status</th><th>Hospital</th></tr></thead>
            <tbody>
            @foreach($incident->patients as $p)
            <tr>
                <td style="font-weight:600">{{ $p->name }}<div style="font-size:.72rem;color:var(--text-muted)">{{ $p->age }} yrs · {{ $p->gender }}</div></td>
                <td><span class="badge badge-{{ $p->triage_level }}">{{ $p->triage_level }}</span></td>
                <td><span class="badge badge-{{ $p->status==='admitted'?'completed':($p->status==='on_scene'?'active':'in_progress') }}">{{ str_replace('_',' ',$p->status) }}</span></td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $p->hospital_name ?? '—' }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#due_datetime_show', {
    enableTime: true,
    dateFormat: 'M d, Y H:i',
    altInput: false,
    minDate: 'today',
    time_24hr: true,
    disableMobile: true,
    onChange: function(selectedDates, dateStr) {
        // Store value in Y-m-d H:i format for Laravel
        if (selectedDates.length) {
            const d = selectedDates[0];
            const pad = n => String(n).padStart(2,'0');
            document.getElementById('due_datetime_val').value =
                `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
        }
    }
});
</script>
@endpush