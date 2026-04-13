{{-- commander/tasks/index.blade.php --}}
@extends('layouts.app')
@section('title','Tasks')
@section('page-title','Task Management')
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
    .flatpickr-day { color: var(--text) !important; border-radius: var(--r-sm) !important; }
    .flatpickr-day:hover { background: var(--surface3) !important; }
    .flatpickr-day.selected { background: var(--accent) !important; border-color: var(--accent) !important; color: #fff !important; }
    .flatpickr-day.today { border-color: var(--accent) !important; color: var(--accent2) !important; }
    .flatpickr-day.flatpickr-disabled { color: var(--text-dim) !important; }
    .flatpickr-months { background: var(--surface3) !important; border-radius: var(--r) var(--r) 0 0 !important; }
    .flatpickr-month, .flatpickr-current-month, .flatpickr-monthDropdown-months,
    .flatpickr-current-month input.cur-year { color: var(--text-bright) !important; fill: var(--text-bright) !important; background: transparent !important; }
    .flatpickr-weekday { color: var(--text-muted) !important; background: transparent !important; }
    .flatpickr-weekdays { background: var(--surface3) !important; }
    .flatpickr-time { background: var(--surface3) !important; border-top: 1px solid var(--border) !important; border-radius: 0 0 var(--r) var(--r) !important; }
    .flatpickr-time input, .flatpickr-time .flatpickr-am-pm { color: var(--text-bright) !important; background: transparent !important; }
    .flatpickr-time input:hover, .flatpickr-time .flatpickr-am-pm:hover { background: var(--surface4) !important; }
    .numInputWrapper span { border-color: var(--border2) !important; }
    .numInputWrapper span svg path { fill: var(--text-muted) !important; }
    .flatpickr-prev-month svg, .flatpickr-next-month svg { fill: var(--text-muted) !important; }
    .flatpickr-prev-month:hover svg, .flatpickr-next-month:hover svg { fill: var(--text-bright) !important; }

    .date-input-wrap { position: relative; }
    .date-input-wrap .cal-icon {
        position: absolute; right: 10px; top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted); pointer-events: none;
        width: 14px; height: 14px;
    }
    .date-input-wrap .form-control { padding-right: 32px; cursor: pointer; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div><h1>All Tasks</h1><div class="bc">Commander / Tasks</div></div>
    <button class="btn btn-primary" onclick="openModal('modal-create-task')">
        <i data-lucide="plus" style="width:14px;height:14px"></i> New Task
    </button>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="margin:0">
            <select name="status" class="form-control">
                <option value="">All Status</option>
                @foreach(['pending','in_progress','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit">
                <i data-lucide="filter" style="width:13px;height:13px"></i> Filter
            </button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Task</th><th>Incident</th><th>Priority</th><th>Assigned To</th><th>Status</th><th>Due</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($tasks as $task)
            @php $isLocked = $task->status === 'completed'; @endphp
            <tr style="{{ $isLocked ? 'opacity:.65' : '' }}">
                <td>
                    <div style="font-weight:600;font-size:.83rem;display:flex;align-items:center;gap:7px">
                        {{ $task->title }}
                        @if($isLocked)
                        <span style="display:inline-flex;align-items:center;gap:3px;font-size:.62rem;font-weight:600;color:var(--green);background:var(--green-dim);border:1px solid rgba(30,201,109,.2);padding:1px 6px;border-radius:10px;letter-spacing:.04em">
                            <i data-lucide="lock" style="width:9px;height:9px"></i> LOCKED
                        </span>
                        @endif
                    </div>
                    @if($task->description)
                    <div style="font-size:.72rem;color:var(--text-muted)">{{ Str::limit($task->description,50) }}</div>
                    @endif
                </td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $task->incident->title }}</td>
                <td><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                <td style="font-size:.82rem">{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                <td><span class="badge badge-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span></td>
                <td style="font-size:.75rem;color:{{ $task->due_datetime && $task->due_datetime->isPast() && !$isLocked ? 'var(--red)' : 'var(--text-muted)' }}">
                    {{ $task->due_datetime?->format('M d, H:i') ?? '—' }}
                </td>
                <td>
                    @if($isLocked)
                        <span style="font-size:.74rem;color:var(--text-dim);display:flex;align-items:center;gap:4px">
                            <i data-lucide="check-circle" style="width:13px;height:13px;color:var(--green)"></i>
                            Completed
                        </span>
                    @else
                        <div style="display:flex;align-items:center;gap:6px">
                            <form method="POST" action="{{ route('commander.tasks.update', $task) }}" style="display:inline">
                                @csrf @method('PUT')
                                <select name="status" class="form-control" style="width:auto;padding:4px;font-size:.73rem;display:inline" onchange="this.form.submit()">
                                    @foreach(['pending','in_progress','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $task->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                    @endforeach
                                </select>
                            </form>
                            <form id="del-t-{{ $task->id }}" method="POST" action="{{ route('commander.tasks.destroy', $task) }}">
                                @csrf @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-xs" onclick="confirmDelete('del-t-{{ $task->id }}')">
                                <i data-lucide="trash-2" style="width:12px;height:12px"></i>
                            </button>
                        </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i data-lucide="check-square"></i><p>No tasks found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $tasks->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>

{{-- Create Task Modal --}}
<div class="modal-backdrop" id="modal-create-task">
    <div class="modal">
        <div class="modal-header">
            <h3>Create New Task</h3>
            <button class="modal-close" onclick="closeModal('modal-create-task')">
                <i data-lucide="x" style="width:16px;height:16px"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('commander.tasks.store') }}">
                @csrf
                <div class="form-group">
                    <label>Incident *</label>
                    <select name="incident_id" class="form-control" required>
                        <option value="">— Select Incident —</option>
                        @foreach($incidents as $inc)
                        <option value="{{ $inc->id }}">{{ $inc->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Task Title *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
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
                        <input type="text" id="task_due_show" class="form-control"
                               placeholder="Select date and time…" readonly>
                        <input type="hidden" name="due_datetime" id="task_due_val">
                        <i data-lucide="calendar" class="cal-icon"></i>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="plus" style="width:14px;height:14px"></i> Create Task
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#task_due_show', {
    enableTime: true,
    dateFormat: 'M d, Y H:i',
    minDate: 'today',
    time_24hr: true,
    disableMobile: true,
    appendTo: document.getElementById('modal-create-task'),
    onChange: function(selectedDates) {
        if (selectedDates.length) {
            const d = selectedDates[0];
            const pad = n => String(n).padStart(2, '0');
            document.getElementById('task_due_val').value =
                `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
        }
    }
});
</script>
@endpush