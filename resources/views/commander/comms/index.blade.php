@extends('layouts.app')
@section('title','Communications')
@section('page-title','Communications Hub')
@section('sidebar-nav')@include('commander._nav')@endsection

@section('content')
<div class="page-header">
    <div><h1>Communications</h1><div class="breadcrumb">Commander / Comms</div></div>
</div>
<div class="grid-2" style="align-items:start">
    <div class="card">
        <div class="card-header"><h2><i data-lucide="send" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Send Message</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('commander.comms.send') }}">
                @csrf
                <div class="form-group">
                    <label>Recipient</label>
                    <select name="receiver_id" class="form-control">
                        <option value="">— Broadcast —</option>
                        @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }} ({{ $u->getRoleLabel() }})</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Channel *</label>
                    <select name="channel" class="form-control">
                        <option value="internal">Internal</option>
                        <option value="radio">Radio</option>
                        <option value="public">Public</option>
                    </select>
                </div>
                <div class="form-group"><label>Message *</label><textarea name="content" class="form-control" rows="4" required placeholder="Type your message…"></textarea></div>
                <button type="submit" class="btn btn-primary"><i data-lucide="send" style="width:14px;height:14px"></i> Send Message</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2><i data-lucide="message-circle" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Message Log</h2></div>
        <div style="max-height:480px;overflow-y:auto;padding:16px">
            @forelse($messages as $msg)
            <div style="margin-bottom:14px;display:flex;gap:10px;{{ $msg->sender_id === auth()->id() ? 'flex-direction:row-reverse' : '' }}">
                <div style="width:32px;height:32px;border-radius:50%;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.72rem;font-weight:700">
                    {{ substr($msg->sender->name, 0, 1) }}
                </div>
                <div style="max-width:80%;{{ $msg->sender_id === auth()->id() ? 'text-align:right' : '' }}">
                    <div style="font-size:.7rem;color:var(--text-muted);margin-bottom:3px">
                        {{ $msg->sender->name }}
                        @if($msg->receiver)<span style="color:var(--accent)">→ {{ $msg->receiver->name }}</span>@else<span style="color:var(--accent)">→ Broadcast</span>@endif
                        · <span class="badge badge-info" style="font-size:.62rem">{{ $msg->channel }}</span>
                        · {{ $msg->created_at->diffForHumans() }}
                    </div>
                    <div style="background:{{ $msg->sender_id === auth()->id() ? 'rgba(232,93,4,.12)' : 'var(--surface2)' }};border:1px solid {{ $msg->sender_id === auth()->id() ? 'rgba(232,93,4,.3)' : 'var(--border)' }};border-radius:8px;padding:10px 12px;font-size:.82rem;line-height:1.5">
                        {{ $msg->content }}
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state"><i data-lucide="message-circle"></i><p>No messages yet.</p></div>
            @endforelse
        </div>
    </div>
</div>
@endsection
