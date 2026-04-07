@extends('layouts.app')
@section('title','Training')
@section('page-title','Training Programs')
@section('sidebar-nav')@include('responder._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>Training</h1><div class="breadcrumb">Responder / Training</div></div>
</div>

<div class="grid-2" style="align-items:start">
    {{-- Available Programs --}}
    <div class="card">
        <div class="card-header"><h2><i data-lucide="graduation-cap" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Available Programs</h2></div>
        <div class="card-body" style="padding:0">
            @forelse($programs as $prog)
            @php $alreadyEnrolled = $myRecords->where('training_program_id', $prog->id)->isNotEmpty(); @endphp
            <div style="padding:16px;border-bottom:1px solid rgba(48,54,61,.5)">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px">
                    <div style="flex:1">
                        <div style="font-weight:600;font-size:.85rem">{{ $prog->title }}</div>
                        @if($prog->description)<div style="font-size:.75rem;color:var(--text-muted);margin-top:3px">{{ Str::limit($prog->description, 80) }}</div>@endif
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px">
                            <span class="badge badge-info">{{ $prog->type }}</span>
                            <span class="badge badge-{{ $prog->status==='upcoming'?'in_progress':($prog->status==='completed'?'completed':'cancelled') }}">{{ $prog->status }}</span>
                            @if($prog->date_scheduled)
                            <span style="font-size:.7rem;color:var(--text-muted)">
                                <i data-lucide="calendar" style="width:10px;height:10px;display:inline"></i>
                                {{ $prog->date_scheduled->format('M d, Y') }}
                            </span>
                            @endif
                        </div>
                        @if($prog->location)
                        <div style="font-size:.72rem;color:var(--text-muted);margin-top:4px">
                            <i data-lucide="map-pin" style="width:10px;height:10px;display:inline"></i> {{ $prog->location }}
                        </div>
                        @endif
                        <div style="font-size:.72rem;color:var(--text-muted);margin-top:3px">
                            {{ $prog->records->count() }}/{{ $prog->max_participants }} enrolled
                        </div>
                    </div>
                    <div style="flex-shrink:0">
                        @if($alreadyEnrolled)
                        <span class="badge badge-completed"><i data-lucide="check" style="width:11px;height:11px"></i> Enrolled</span>
                        @elseif($prog->status === 'upcoming' && $prog->records->count() < $prog->max_participants)
                        <form method="POST" action="{{ route('responder.training.enroll') }}">
                            @csrf
                            <input type="hidden" name="training_program_id" value="{{ $prog->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i data-lucide="plus" style="width:13px;height:13px"></i> Enroll
                            </button>
                        </form>
                        @else
                        <span class="badge badge-cancelled">Unavailable</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:24px"><i data-lucide="graduation-cap"></i><p>No programs available.</p></div>
            @endforelse
        </div>
    </div>

    {{-- My Training Records --}}
    <div class="card">
        <div class="card-header"><h2><i data-lucide="clipboard-list" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>My Training History</h2></div>
        @if($myRecords->isEmpty())
        <div class="empty-state" style="padding:32px"><i data-lucide="clipboard-list"></i><p>No training records yet. Enroll in a program to get started.</p></div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Program</th><th>Type</th><th>Status</th><th>Score</th><th>Completed</th></tr></thead>
                <tbody>
                @foreach($myRecords as $rec)
                <tr>
                    <td style="font-weight:600;font-size:.83rem">{{ $rec->program->title }}</td>
                    <td><span class="badge badge-info">{{ $rec->program->type }}</span></td>
                    <td>
                        <span class="badge badge-{{ $rec->status==='passed'?'completed':($rec->status==='enrolled'?'in_progress':($rec->status==='failed'?'active':'cancelled')) }}">
                            {{ $rec->status }}
                        </span>
                    </td>
                    <td style="font-size:.82rem">
                        @if($rec->score)
                        <span style="font-weight:600;color:{{ $rec->score >= 75 ? 'var(--green)' : 'var(--red)' }}">{{ $rec->score }}%</span>
                        @else —
                        @endif
                    </td>
                    <td style="font-size:.75rem;color:var(--text-muted)">
                        {{ $rec->date_completed?->format('M d, Y') ?? '—' }}
                        @if($rec->certificate_issued)
                        <span class="badge badge-completed" style="margin-left:4px;font-size:.62rem"><i data-lucide="award" style="width:10px;height:10px"></i> Cert</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
