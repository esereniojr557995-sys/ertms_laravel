@extends('layouts.app')
@section('title', $incident->title)
@section('page-title','Incident Detail')
@section('sidebar-nav')@include('responder._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>{{ $incident->title }}</h1><div class="breadcrumb">Responder / Incidents / Detail</div></div>
    <a href="{{ route('responder.incidents') }}" class="btn btn-secondary"><i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back</a>
</div>

<div class="grid-2" style="align-items:start">
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
                ['Commander', $incident->commander?->name ?? 'Not assigned'],
                ['Reported At', $incident->date_reported->format('M d, Y H:i')],
            ] as [$label,$value])
            <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(48,54,61,.5);font-size:.82rem">
                <span style="color:var(--text-muted)">{{ $label }}</span>
                <span style="font-weight:500">{{ $value }}</span>
            </div>
            @endforeach
            @if($incident->description)
            <div style="margin-top:14px">
                <div style="font-size:.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Description</div>
                <div style="font-size:.82rem;line-height:1.6">{{ $incident->description }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2><i data-lucide="check-square" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Tasks for this Incident</h2></div>
        <div class="card-body" style="padding:0">
            @forelse($incident->tasks as $task)
            <div style="padding:12px 16px;border-bottom:1px solid rgba(48,54,61,.5)">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                    <div>
                        <div style="font-weight:600;font-size:.83rem">{{ $task->title }}</div>
                        @if($task->description)<div style="font-size:.72rem;color:var(--text-muted);margin-top:2px">{{ $task->description }}</div>@endif
                        <div style="margin-top:5px;display:flex;gap:6px;align-items:center">
                            <span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span>
                            <span style="font-size:.72rem;color:var(--text-muted)">{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                        </div>
                    </div>
                    <span class="badge badge-{{ $task->status }}" style="flex-shrink:0">{{ str_replace('_',' ',$task->status) }}</span>
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:24px"><p>No tasks assigned to this incident.</p></div>
            @endforelse
        </div>
    </div>
</div>
@endsection
