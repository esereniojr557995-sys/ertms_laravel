@extends('layouts.app')

@section('title', 'Admin Communications')
@section('page-title', 'Communications Hub')

@section('sidebar-nav')
   <div class="sb-section">Operations</div>
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i data-lucide="layout-dashboard"></i> Dashboard
    </a>
    <a href="{{ route('admin.incidents') }}" class="{{ request()->routeIs('admin.incidents*') ? 'active' : '' }}">
        <i data-lucide="flame"></i> Incidents
    </a>
    <a href="{{ route('admin.citizen_reports') }}" class="active"><i data-lucide="file-text"></i> Citizen Reports</a>
    <a href="{{ route('admin.resources') }}" class="{{ request()->routeIs('admin.resources*') ? 'active' : '' }}">
        <i data-lucide="package"></i> Resources
    </a>
    <a href="{{ route('admin.alerts') }}" class="{{ request()->routeIs('admin.alerts*') ? 'active' : '' }}">
        <i data-lucide="bell"></i> Alerts
    </a>
    <a href="{{ route('admin.comms') }}" class="{{ request()->routeIs('admin.comms*') ? 'active' : '' }}">
        <i data-lucide="message-square"></i> Communications
    </a>
    <a href="{{ route('admin.patients') }}" class="{{ request()->routeIs('admin.patients*') ? 'active' : '' }}">
        <i data-lucide="heart-pulse"></i> Medical
    </a>
    <a href="{{ route('admin.training') }}" class="{{ request()->routeIs('admin.training*') ? 'active' : '' }}">
        <i data-lucide="graduation-cap"></i> Training
    </a>
    <a href="{{ route('admin.shelters') }}" class="{{ request()->routeIs('admin.shelters*') ? 'active' : '' }}">
        <i data-lucide="map-pin"></i> Shelters
    </a>

    <div class="sb-section">Management</div>
    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
        <i data-lucide="users"></i> Users
    </a>
    <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
        <i data-lucide="bar-chart-2"></i> Reports
    </a>
    <a href="{{ route('admin.audit_logs') }}" class="{{ request()->routeIs('admin.audit_logs') ? 'active' : '' }}">
        <i data-lucide="scroll-text"></i> Audit Logs
    </a>
    <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        <i data-lucide="settings"></i> Settings
    </a>
@endsection

@push('styles')
<style>
    .comms-layout{display:grid;grid-template-columns:260px 1fr;gap:0;height:calc(100vh - 160px);min-height:500px;margin-top:10px;}
    .comms-sidebar{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius) 0 0 var(--radius);display:flex;flex-direction:column;overflow:hidden;}
    .comms-sidebar-head{padding:14px 16px;border-bottom:1px solid var(--border);flex-shrink:0;}
    .comms-sidebar-head h3{font-family:'Syne',sans-serif;font-size:.82rem;font-weight:700;color:var(--text);margin:0;}
    .comms-sidebar-head p{font-size:.68rem;color:var(--text-muted);margin-top:2px;}
    
    .contact-list{flex:1;overflow-y:auto;scrollbar-width:none;}
    .contact-item, .group-item{display:flex;align-items:center;gap:10px;padding:12px 14px;cursor:pointer;border-bottom:1px solid rgba(30,45,66,.4);transition:background .1s;text-decoration:none;color:inherit;}
    .contact-item:hover, .group-item:hover{background:rgba(255,255,255,.03);}
    .contact-item.active, .group-item.active{background:var(--accent-glow);border-right:2px solid var(--accent);}
    
    .avatar{width:32px;height:32px;border-radius:50%;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;}
    .active .avatar{background:rgba(255,77,28,.15);border-color:rgba(255,77,28,.3);color:var(--accent);}
    
    .gicon{width:32px;height:32px;border-radius:8px;background:rgba(59,158,255,.1);border:1px solid rgba(59,158,255,.2);display:flex;align-items:center;justify-content:center;color:var(--blue);}
    
    .comms-chat{background:var(--bg);border:1px solid var(--border);border-left:none;border-radius:0 var(--radius) var(--radius) 0;display:flex;flex-direction:column;overflow:hidden;}
    .chat-header{padding:12px 18px;border-bottom:1px solid var(--border);background:var(--surface);display:flex;align-items:center;gap:10px;}
    .chat-messages{flex:1;overflow-y:auto;padding:16px 18px;display:flex;flex-direction:column;gap:10px;}
    
    .msg-row{display:flex;gap:8px;align-items:flex-end;}
    .msg-row.mine{flex-direction:row-reverse;}
    .msg-bubble{padding:9px 12px;border-radius:12px;font-size:.82rem;line-height:1.5;max-width:75%;word-wrap:break-word;}
    .msg-row:not(.mine) .msg-bubble{background:var(--surface);border:1px solid var(--border);border-bottom-left-radius:3px;color:var(--text);}
    .msg-row.mine .msg-bubble{background:rgba(255,77,28,.12);border:1px solid rgba(255,77,28,.2);border-bottom-right-radius:3px;color:var(--text);}
    
    .msg-meta{font-size:.65rem;color:var(--text-muted);margin-bottom:3px;display:flex;gap:5px;}
    .msg-row.mine .msg-meta{justify-content:flex-end;}

    .chat-input-area{padding:12px 16px;border-top:1px solid var(--border);background:var(--surface);}
    .chat-input-wrap{display:flex;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:5px 10px;align-items:center;}
    .chat-textarea{flex:1;background:transparent;border:none;outline:none;color:var(--text);font-size:.85rem;resize:none;padding:8px 0;}
    .chat-send-btn{background:var(--accent);color:white;border:none;width:34px;height:34px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;}
</style>
@endpush

@section('content')
<div class="comms-layout">
    <div class="comms-sidebar">
        <div class="comms-sidebar-head">
            <h3>Messages</h3>
            <p>{{ now()->format('D, M d · H:i') }}</p>
        </div>
        <div class="contact-list">
            <a href="{{ route('admin.comms') }}" class="group-item {{ !request('with') ? 'active' : '' }}">
                <div class="gicon"><i data-lucide="radio" style="width:14px;"></i></div>
                <div>
                    <div style="font-size:.8rem; font-weight:600;">Group Channel</div>
                    <div style="font-size:.65rem; color:var(--text-muted);">Broadcast to all units</div>
                </div>
            </a>

            <div style="padding:15px 14px 5px; font-size:.6rem; font-weight:700; color:var(--accent); text-transform:uppercase; letter-spacing:.1em">Active Units</div>
            @foreach($users as $u)
                @if($u->id !== auth()->id())
                <a href="{{ route('admin.comms') }}?with={{ $u->id }}" class="contact-item {{ request('with') == $u->id ? 'active' : '' }}">
                    <div class="avatar">{{ substr($u->name, 0, 1) }}</div>
                    <div class="cinfo">
                        <div style="font-size:.8rem; font-weight:600;">{{ $u->name }}</div>
                        <div style="font-size:.65rem; color:var(--text-muted);">{{ $u->role }}</div>
                    </div>
                </a>
                @endif
            @endforeach
        </div>
    </div>

    <div class="comms-chat">
        <div class="chat-header">
            {{-- SAFEGUARD: Defensive check for $withUser --}}
            @if(isset($withUser) && $withUser)
                <div class="avatar">{{ substr($withUser->name, 0, 1) }}</div>
                <div>
                    <div style="font-size:.85rem; font-weight:700;">{{ $withUser->name }}</div>
                    <div style="font-size:.65rem; color:var(--text-muted);">{{ $withUser->role }} · Secure Line</div>
                </div>
            @else
                <div class="gicon"><i data-lucide="radio" style="width:14px;"></i></div>
                <div>
                    <div style="font-size:.85rem; font-weight:700;">Group Channel</div>
                    <div style="font-size:.65rem; color:var(--text-muted);">Public Broadcast System</div>
                </div>
            @endif
        </div>

        <div class="chat-messages" id="chat-messages">
            @forelse($messages as $msg)
                <div class="msg-row {{ $msg->sender_id === auth()->id() ? 'mine' : '' }}" data-id="{{ $msg->id }}">
                    <div class="msg-bubble-wrap">
                        <div class="msg-meta">
                            <strong>{{ $msg->sender->name }}</strong> · {{ $msg->created_at->diffForHumans() }}
                        </div>
                        <div class="msg-bubble">{{ $msg->content }}</div>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding-top:100px; opacity:0.3;">
                    <i data-lucide="message-square" style="width:48px; height:48px; margin:0 auto;"></i>
                    <p>No messages in this frequency</p>
                </div>
            @endforelse
        </div>

        <div class="chat-input-area">
            <div class="chat-input-wrap">
                <textarea class="chat-textarea" id="msg-input" placeholder="Enter secure message..." rows="1"></textarea>
                <button class="chat-send-btn" id="send-btn"><i data-lucide="send" style="width:16px;"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const SEND_URL  = "{{ route('admin.comms.send') }}";
const FETCH_URL = "{{ route('admin.comms.fetch') }}";
const WITH_ID   = "{{ request('with') }}";
const CSRF      = document.querySelector('meta[name=csrf-token]').getAttribute('content');
const ME_ID     = {{ auth()->id() }};

let lastMsgId = {{ $messages->count() > 0 ? $messages->last()->id : 0 }};
const container = document.getElementById('chat-messages');
const input     = document.getElementById('msg-input');
const sendBtn   = document.getElementById('send-btn');

function scrollToBottom() { container.scrollTop = container.scrollHeight; }
scrollToBottom();

async function sendMessage() {
    const content = input.value.trim();
    if (!content) return;

    input.value = '';
    try {
        await fetch(SEND_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ content, receiver_id: WITH_ID })
        });
        pollMessages(); 
    } catch(e) { console.error('Send error', e); }
}

async function pollMessages() {
    try {
        let url = `${FETCH_URL}?since=${lastMsgId}`;
        if (WITH_ID) url += `&with=${WITH_ID}`;
        
        const res = await fetch(url);
        const msgs = await res.json();
        
        if (msgs.length > 0) {
            msgs.forEach(m => {
                if (!document.querySelector(`[data-id="${m.id}"]`)) {
                    const div = document.createElement('div');
                    const isMine = m.sender_id === ME_ID;
                    div.className = `msg-row ${isMine ? 'mine' : ''}`;
                    div.setAttribute('data-id', m.id);
                    div.innerHTML = `
                        <div class="msg-bubble-wrap">
                            <div class="msg-meta"><strong>${m.sender_name || 'System'}</strong></div>
                            <div class="msg-bubble">${m.content}</div>
                        </div>`;
                    container.appendChild(div);
                    lastMsgId = m.id;
                }
            });
            scrollToBottom();
        }
    } catch(e) { /* silent */ }
}

sendBtn.addEventListener('click', sendMessage);
input.addEventListener('keydown', e => { if(e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); } });

setInterval(pollMessages, 3000);
lucide.createIcons();
</script>
@endpush