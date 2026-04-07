@extends('layouts.app')
@section('title','Report Detail')
@section('page-title','Report Detail')
@section('sidebar-nav')@include('citizen._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>Report Detail</h1><div class="breadcrumb">Citizen / Portal / Detail</div></div>
    <a href="{{ route('citizen.portal') }}" class="btn btn-secondary"><i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back</a>
</div>

<div class="card" style="max-width:640px">
    <div class="card-header">
        <h2>{{ $report->title }}</h2>
        <span class="badge badge-{{ $report->status==='acknowledged'?'in_progress':($report->status==='resolved'?'completed':($report->status==='dismissed'?'cancelled':'pending')) }}">
            {{ $report->status }}
        </span>
    </div>
    <div class="card-body">
        @foreach([
            ['Type', ucfirst($report->type)],
            ['Location', $report->location],
            ['Submitted', $report->created_at->format('M d, Y H:i')],
            ['Last Updated', $report->updated_at->diffForHumans()],
        ] as [$label,$value])
        <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(48,54,61,.5);font-size:.82rem">
            <span style="color:var(--text-muted)">{{ $label }}</span>
            <span style="font-weight:500">{{ $value }}</span>
        </div>
        @endforeach

        <div style="margin-top:16px">
            <div style="font-size:.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Description</div>
            <div style="font-size:.83rem;line-height:1.7;background:var(--surface2);border-radius:6px;padding:14px;border:1px solid var(--border)">{{ $report->description }}</div>
        </div>

        @if($report->photo)
        <div style="margin-top:16px">
            <div style="font-size:.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Photo</div>
            <img src="{{ asset('storage/'.$report->photo) }}" alt="Report photo" style="max-width:100%;border-radius:8px;border:1px solid var(--border)">
        </div>
        @endif

        @if($report->status === 'pending')
        <div style="margin-top:20px;background:rgba(210,153,34,.08);border:1px solid rgba(210,153,34,.25);border-radius:6px;padding:12px 14px;font-size:.8rem;color:var(--yellow)">
            <i data-lucide="clock" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>
            Your report is pending review by the emergency response team.
        </div>
        @elseif($report->status === 'acknowledged')
        <div style="margin-top:20px;background:rgba(56,139,253,.08);border:1px solid rgba(56,139,253,.25);border-radius:6px;padding:12px 14px;font-size:.8rem;color:var(--blue)">
            <i data-lucide="check-circle" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>
            Your report has been acknowledged. The response team is aware of this incident.
        </div>
        @elseif($report->status === 'resolved')
        <div style="margin-top:20px;background:rgba(63,185,80,.08);border:1px solid rgba(63,185,80,.25);border-radius:6px;padding:12px 14px;font-size:.8rem;color:var(--green)">
            <i data-lucide="check-circle-2" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>
            This report has been resolved. Thank you for helping keep our community safe.
        </div>
        @endif
    </div>
</div>
@endsection
