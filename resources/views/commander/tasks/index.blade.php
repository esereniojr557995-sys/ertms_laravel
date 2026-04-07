{{-- commander/tasks/index.blade.php --}}
@extends('layouts.app')
@section('title','Tasks')
@section('page-title','Task Management')
@section('sidebar-nav')@include('commander._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>All Tasks</h1><div class="breadcrumb">Commander / Tasks</div></div>
    <button class="btn btn-primary" onclick="openModal('modal-create-task')"><i data-lucide="plus" style="width:14px;height:14px"></i> New Task</button>
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
            <button class="btn btn-secondary" type="submit"><i data-lucide="filter" style="width:13px;height:13px"></i> Filter</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Task</th><th>Incident</th><th>Priority</th><th>Assigned To</th><th>Status</th><th>Due</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($tasks as $task)
            <tr>
                <td>
                    <div style="font-weight:600;font-size:.83rem">{{ $task->title }}</div>
                    @if($task->description)<div style="font-size:.72rem;color:var(--text-muted)">{{ Str::limit($task->description,50) }}</div>@endif
                </td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $task->incident->title }}</td>
                <td><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                <td style="font-size:.82rem">{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                <td><span class="badge badge-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span></td>
                <td style="font-size:.75rem;color:{{ $task->due_datetime && $task->due_datetime->isPast() && $task->status !== 'completed' ? 'var(--red)' : 'var(--text-muted)' }}">
                    {{ $task->due_datetime?->format('M d, H:i') ?? '—' }}
                </td>
                <td>
                    <form method="POST" action="{{ route('commander.tasks.update', $task) }}" style="display:inline">
                        @csrf @method('PUT')
                        <select name="status" class="form-control" style="width:auto;padding:4px;font-size:.73rem;display:inline" onchange="this.form.submit()">
                            @foreach(['pending','in_progress','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $task->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </form>
                    <form id="del-t-{{ $task->id }}" method="POST" action="{{ route('commander.tasks.destroy', $task) }}" style="display:inline">@csrf @method('DELETE')</form>
                    <button class="btn btn-danger btn-xs" style="margin-left:4px" onclick="confirmDelete('del-t-{{ $task->id }}')"><i data-lucide="trash-2" style="width:12px;height:12px"></i></button>
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

<div class="modal-backdrop" id="modal-create-task">
    <div class="modal">
        <div class="modal-header"><h3>Create New Task</h3><button class="modal-close" onclick="closeModal('modal-create-task')"><i data-lucide="x" style="width:16px;height:16px"></i></button></div>
        <div class="modal-body">
            <form method="POST" action="{{ route('commander.tasks.store') }}">
                @csrf
                <div class="form-group">
                    <label>Incident *</label>
                    <select name="incident_id" class="form-control" required>
                        <option value="">— Select Incident —</option>
                        @foreach($incidents as $inc)<option value="{{ $inc->id }}">{{ $inc->title }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group"><label>Task Title *</label><input type="text" name="title" class="form-control" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Priority *</label>
                        <select name="priority" class="form-control">
                            @foreach(['low','medium','high','critical'] as $p)<option value="{{ $p }}">{{ ucfirst($p) }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Assign To</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">— Unassigned —</option>
                            @foreach($responders as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group"><label>Due Date/Time</label><input type="datetime-local" name="due_datetime" class="form-control"></div>
                <button type="submit" class="btn btn-primary"><i data-lucide="plus" style="width:14px;height:14px"></i> Create Task</button>
            </form>
        </div>
    </div>
</div>
@endsection
