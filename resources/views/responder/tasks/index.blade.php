@extends('layouts.app')
@section('title','My Tasks')
@section('page-title','My Assigned Tasks')
@section('sidebar-nav')@include('responder._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>My Tasks</h1><div class="breadcrumb">Responder / Tasks</div></div>
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
        <span style="color:var(--text-muted);font-size:.78rem">{{ $tasks->total() }} tasks</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Task</th><th>Incident</th><th>Priority</th><th>Status</th><th>Due</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($tasks as $task)
            <tr>
                <td>
                    <div style="font-weight:600;font-size:.83rem">{{ $task->title }}</div>
                    @if($task->description)<div style="font-size:.72rem;color:var(--text-muted)">{{ Str::limit($task->description,60) }}</div>@endif
                </td>
                <td style="font-size:.78rem;color:var(--text-muted)">{{ $task->incident->title }}</td>
                <td>
                    <span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span>
                </td>
                <td style="font-size:.75rem;color:{{ $task->due_datetime && $task->due_datetime->isPast() && $task->status !== 'completed' ? 'var(--red)' : 'var(--text-muted)' }}">
                    {{ $task->due_datetime?->format('M d, H:i') ?? '—' }}
                    @if($task->due_datetime && $task->due_datetime->isPast() && $task->status !== 'completed')
                    <span style="font-size:.68rem;display:block;color:var(--red)">OVERDUE</span>
                    @endif
                </td>
                <td>
                    @if(in_array($task->status, ['pending','in_progress']))
                    <div style="display:flex;gap:5px">
                        @if($task->status === 'pending')
                        <form method="POST" action="{{ route('responder.tasks.update_status', $task) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="btn btn-secondary btn-xs">
                                <i data-lucide="play" style="width:12px;height:12px"></i> Start
                            </button>
                        </form>
                        @endif
                        @if($task->status === 'in_progress')
                        <form method="POST" action="{{ route('responder.tasks.update_status', $task) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success btn-xs">
                                <i data-lucide="check" style="width:12px;height:12px"></i> Complete
                            </button>
                        </form>
                        @endif
                    </div>
                    @elseif($task->status === 'completed')
                    <div style="display:flex;align-items:center;gap:5px;color:var(--green);font-size:.78rem">
                        <i data-lucide="check-circle" style="width:13px;height:13px"></i>
                        Done {{ $task->completed_at?->format('M d') }}
                    </div>
                    @else
                    <span style="color:var(--text-muted);font-size:.78rem">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i data-lucide="check-square"></i><p>No tasks assigned to you.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px">{{ $tasks->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>
@endsection
