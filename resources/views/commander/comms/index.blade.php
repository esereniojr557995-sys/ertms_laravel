@extends('layouts.app')
@section('title','Communications')
@section('page-title','Communications Hub')
@section('sidebar-nav')@include('commander._nav')@endsection

@push('styles')
<style>
.comms-layout{display:grid;grid-template-columns:240px 1fr;gap:0;height:calc(100vh - var(--topbar-h) - 44px);min-height:500px;}
.comms-sidebar{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius) 0 0 var(--radius);display:flex;flex-direction:column;overflow:hidden;}
.comms-sidebar-head{padding:14px 16px;border-bottom:1px solid var(--border);flex-shrink:0;}
.comms-sidebar-head h3{font-family:'Syne',sans-serif;font-size:.82rem;font-weight:700;color:var(--text);}
.comms-sidebar-head p{font-size:.68rem;color:var(--text-muted);margin-top:2px;}
.contact-list{flex:1;overflow-y:auto;}
.contact-item{display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;border-bottom:1px solid rgba(30,45,66,.4);transition:background .1s;text-decoration:none;}
.contact-item:hover{background:rgba(255,255,255,.03);}
.contact-item.active{background:var(--accent-glow);border-right:2px solid var(--accent);}
.contact-item .avatar{width:32px;height:32px;border-radius:50%;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;}
.contact-item.active .avatar{background:rgba(255,77,28,.15);border-color:rgba(255,77,28,.3);color:var(--accent2);}
.contact-item .cinfo{flex:1;min-width:0;}
.contact-item .cname{font-size:.79rem;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.contact-item .clast{font-size:.67rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:1px;}
.contact-item .cbadge{background:var(--red);color:#fff;font-size:.58rem;font-weight:700;padding:1px 5px;border-radius:8px;flex-shrink:0;}
.group-item{display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;border-bottom:1px solid rgba(30,45,66,.4);transition:background .1s;text-decoration:none;}
.group-item:hover{background:rgba(255,255,255,.03);}
.group-item.active{background:var(--accent-glow);border-right:2px solid var(--accent);}
.group-item .gicon{width:32px;height:32px;border-radius:8px;background:rgba(59,158,255,.1);border:1px solid rgba(59,158,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--blue);}
.group-item .gname{font-size:.79rem;font-weight:600;color:var(--text);}
.group-item .gsub{font-size:.67rem;color:var(--text-muted);margin-top:1px;}

/* Chat area */
.comms-chat{background:var(--bg);border:1px solid var(--border);border-left:none;border-radius:0 var(--radius) var(--radius) 0;display:flex;flex-direction:column;overflow:hidden;}
.chat-header{padding:12px 18px;border-bottom:1px solid var(--border);background:var(--surface);display:flex;align-items:center;gap:10px;flex-shrink:0;}
.chat-header .ch-avatar{width:34px;height:34px;border-radius:50%;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:700;flex-shrink:0;}
.chat-header .ch-name{font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;color:var(--text);}
.chat-header .ch-role{font-size:.68rem;color:var(--text-muted);margin-top:1px;}
.chat-header .live-badge{margin-left:auto;display:flex;align-items:center;gap:5px;font-size:.67rem;color:var(--green);background:var(--green-soft);border:1px solid rgba(34,208,122,.15);padding:3px 8px;border-radius:10px;}
.chat-header .live-badge .dot{width:5px;height:5px;background:var(--green);border-radius:50%;animation:breathe 2s infinite;}

/* Messages */
.chat-messages{flex:1;overflow-y:auto;padding:16px 18px;display:flex;flex-direction:column;gap:10px;scrollbar-width:thin;scrollbar-color:var(--border2) transparent;}
.msg-row{display:flex;gap:8px;align-items:flex-end;}
.msg-row.mine{flex-direction:row-reverse;}
.msg-avatar{width:26px;height:26px;border-radius:50%;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0;}
.msg-bubble-wrap{max-width:70%;}
.msg-meta{font-size:.63rem;color:var(--text-muted);margin-bottom:3px;display:flex;align-items:center;gap:5px;}
.msg-row.mine .msg-meta{justify-content:flex-end;}
.msg-bubble{padding:9px 12px;border-radius:12px;font-size:.8rem;line-height:1.55;word-break:break-word;}
.msg-row:not(.mine) .msg-bubble{background:var(--surface);border:1px solid var(--border);border-bottom-left-radius:3px;color:var(--text);}
.msg-row.mine .msg-bubble{background:rgba(255,77,28,.12);border:1px solid rgba(255,77,28,.2);border-bottom-right-radius:3px;color:var(--text);}
.msg-channel{display:inline-block;padding:1px 5px;border-radius:4px;font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;}
.msg-channel.internal{background:rgba(59,158,255,.12);color:var(--blue);}
.msg-channel.radio{background:rgba(245,183,49,.12);color:var(--yellow);}
.msg-channel.public{background:rgba(34,208,122,.1);color:var(--green);}

/* Input area */
.chat-input-area{padding:12px 16px;border-top:1px solid var(--border);background:var(--surface);flex-shrink:0;}
.chat-input-row{display:flex;gap:8px;align-items:flex-end;}
.chat-input-wrap{flex:1;background:var(--surface2);border:1px solid var(--border);border-radius:10px;display:flex;align-items:flex-end;gap:0;transition:border-color .15s;}
.chat-input-wrap:focus-within{border-color:var(--accent);box-shadow:0 0 0 2px rgba(255,77,28,.08);}
.chat-textarea{flex:1;background:transparent;border:none;outline:none;resize:none;color:var(--text);font-size:.81rem;font-family:'DM Sans',sans-serif;padding:10px 12px;max-height:120px;min-height:40px;line-height:1.5;}
.chat-textarea::placeholder{color:var(--text-muted);}
.channel-select{background:transparent;border:none;outline:none;color:var(--text-muted);font-size:.7rem;font-family:'DM Sans',sans-serif;padding:6px 8px;cursor:pointer;border-left:1px solid var(--border);align-self:stretch;}
.chat-send-btn{width:38px;height:38px;border-radius:9px;background:var(--accent);border:none;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .12s;flex-shrink:0;box-shadow:0 2px 8px rgba(255,77,28,.25);}
.chat-send-btn:hover{background:#ff3a00;transform:translateY(-1px);}
.chat-send-btn svg{width:15px;height:15px;}
.chat-send-btn:disabled{opacity:.5;cursor:not-allowed;transform:none;}
.typing-indicator{font-size:.68rem;color:var(--text-muted);height:16px;padding:0 4px;}
.empty-chat{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--text-muted);gap:10px;padding:40px;}
.empty-chat svg{width:40px;height:40px;opacity:.15;}
.empty-chat p{font-size:.82rem;}
</style>
@endpush

@section('content')
<div class="comms-layout">

    {{-- ── Sidebar: contacts ── --}}
    <div class="comms-sidebar">
        <div class="comms-sidebar-head">
            <h3>Messages</h3>
            <p>{{ now()->format('D, M d · H:i') }}</p>
        </div>
        <div class="contact-list">
            {{-- Group / Broadcast channel --}}
            <a href="{{ route('commander.comms') }}" class="group-item {{ !$withUser ? 'active' : '' }}">
                <div class="gicon"><i data-lucide="radio" style="width:14px;height:14px"></i></div>
                <div>
                    <div class="gname">Group Channel</div>
                    <div class="gsub">All broadcasts & team</div>
                </div>
            </a>

            {{-- Individual conversations --}}
            @foreach($conversations as $conv)
            <a href="{{ route('commander.comms') }}?with={{ $conv['user']->id }}" class="contact-item {{ $withUser?->id == $conv['user']->id ? 'active' : '' }}">
                <div class="avatar">{{ substr($conv['user']->name, 0, 1) }}</div>
                <div class="cinfo">
                    <div class="cname">{{ $conv['user']->name }}</div>
                    <div class="clast">{{ $conv['last_msg'] ? Str::limit($conv['last_msg'], 28) : 'No messages' }}</div>
                </div>
                @if($conv['unread'] > 0)
                <span class="cbadge">{{ $conv['unread'] }}</span>
                @endif
            </a>
            @endforeach

            {{-- New conversation: all available users not yet in conversations --}}
            @php
            $convUserIds = collect($conversations)->pluck('user.id')->toArray();
            $newUsers = $users->filter(fn($u) => !in_array($u->id, $convUserIds));
            @endphp
            @if($newUsers->count() > 0)
            <div style="padding:8px 14px 4px;font-size:.6rem;font-weight:700;color:var(--text-dim);text-transform:uppercase;letter-spacing:.1em">New Message</div>
            @foreach($newUsers as $u)
            <a href="{{ route('commander.comms') }}?with={{ $u->id }}" class="contact-item {{ $withUser?->id == $u->id ? 'active' : '' }}">
                <div class="avatar">{{ substr($u->name, 0, 1) }}</div>
                <div class="cinfo">
                    <div class="cname">{{ $u->name }}</div>
                    <div class="clast">{{ $u->getRoleLabel() }}</div>
                </div>
            </a>
            @endforeach
            @endif
        </div>
    </div>

    {{-- ── Chat area ── --}}
    <div class="comms-chat">
        {{-- Header --}}
        <div class="chat-header">
            @if($withUser)
            <div class="ch-avatar">{{ substr($withUser->name, 0, 1) }}</div>
            <div>
                <div class="ch-name">{{ $withUser->name }}</div>
                <div class="ch-role">{{ $withUser->getRoleLabel() }} · {{ $withUser->unit ?? 'No unit' }}</div>
            </div>
            @else
            <div class="ch-avatar" style="background:rgba(59,158,255,.1);border-color:rgba(59,158,255,.2);color:var(--blue)">
                <i data-lucide="radio" style="width:14px;height:14px"></i>
            </div>
            <div>
                <div class="ch-name">Group Channel</div>
                <div class="ch-role">Broadcasts visible to all team members</div>
            </div>
            @endif
            <div class="live-badge"><div class="dot"></div>Live</div>
        </div>

        {{-- Messages --}}
        <div class="chat-messages" id="chat-messages">
            @forelse($messages as $msg)
            <div class="msg-row {{ $msg->sender_id === auth()->id() ? 'mine' : '' }}" data-id="{{ $msg->id }}">
                <div class="msg-avatar">{{ substr($msg->sender->name, 0, 1) }}</div>
                <div class="msg-bubble-wrap">
                    <div class="msg-meta">
                        <span>{{ $msg->sender->name }}</span>
                        @if($msg->receiver)<span style="color:var(--accent2)">→ {{ $msg->receiver->name }}</span>@else<span style="color:var(--text-dim)">→ All</span>@endif
                        <span class="msg-channel {{ $msg->channel }}">{{ $msg->channel }}</span>
                        <span>{{ $msg->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="msg-bubble">{{ $msg->content }}</div>
                </div>
            </div>
            @empty
            <div class="empty-chat">
                <i data-lucide="message-circle"></i>
                <p>No messages yet. Start the conversation.</p>
            </div>
            @endforelse
        </div>
        <div class="typing-indicator" id="typing-indicator"></div>

        {{-- Input --}}
        <div class="chat-input-area">
            <div class="chat-input-row">
                <div class="chat-input-wrap">
                    <textarea class="chat-textarea" id="msg-input" rows="1" placeholder="{{ $withUser ? 'Message '.$withUser->name.'…' : 'Broadcast to all team members…' }}" maxlength="1000"></textarea>
                    <select class="channel-select" id="channel-select">
                        <option value="internal">Internal</option>
                        <option value="radio">Radio</option>
                        <option value="public">Public</option>
                    </select>
                </div>
                <button class="chat-send-btn" id="send-btn" title="Send (Enter)">
                    <i data-lucide="send"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const SEND_URL   = "{{ route('commander.comms.send') }}";
const FETCH_URL  = "{{ route('commander.comms.fetch') }}";
const WITH_ID    = "{{ $withUser?->id ?? '' }}";
const CSRF       = document.querySelector('meta[name=csrf-token]').getAttribute('content');
const ME_ID      = {{ auth()->id() }};

let lastMsgId = {{ $messages->isNotEmpty() ? $messages->last()->id : 0 }};
let polling;

const container = document.getElementById('chat-messages');
const input     = document.getElementById('msg-input');
const sendBtn   = document.getElementById('send-btn');
const indicator = document.getElementById('typing-indicator');

// Auto-resize textarea
input.addEventListener('input', () => {
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 120) + 'px';
});

// Send on Enter (Shift+Enter = newline)
input.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});
sendBtn.addEventListener('click', sendMessage);

function scrollToBottom() {
    container.scrollTop = container.scrollHeight;
}
scrollToBottom();

function buildMsgHTML(m) {
    const mine = m.is_mine || m.sender_id === ME_ID;
    const recv = m.receiver_name ? `<span style="color:var(--accent2)">→ ${m.receiver_name}</span>` : `<span style="color:var(--text-dim)">→ All</span>`;
    return `<div class="msg-row ${mine ? 'mine' : ''}" data-id="${m.id}">
        <div class="msg-avatar">${m.sender_name.charAt(0)}</div>
        <div class="msg-bubble-wrap">
            <div class="msg-meta">
                <span>${m.sender_name}</span>${recv}
                <span class="msg-channel ${m.channel}">${m.channel}</span>
                <span>${m.time}</span>
            </div>
            <div class="msg-bubble">${escHtml(m.content)}</div>
        </div>
    </div>`;
}

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/\n/g,'<br>');
}

function appendMessage(m) {
    // Remove empty state if present
    const empty = container.querySelector('.empty-chat');
    if (empty) empty.remove();
    const div = document.createElement('div');
    div.innerHTML = buildMsgHTML(m);
    container.appendChild(div.firstElementChild);
    if (m.id > lastMsgId) lastMsgId = m.id;
}

async function sendMessage() {
    const content = input.value.trim();
    if (!content) return;
    sendBtn.disabled = true;
    input.value = '';
    input.style.height = 'auto';

    try {
        const body = new FormData();
        body.append('_token', CSRF);
        body.append('content', content);
        body.append('channel', document.getElementById('channel-select').value);
        if (WITH_ID) body.append('receiver_id', WITH_ID);

        const res  = await fetch(SEND_URL, { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body });
        const data = await res.json();
        appendMessage(data);
        scrollToBottom();
    } catch(e) {
        input.value = content; // restore on error
        console.error('Send failed', e);
    }
    sendBtn.disabled = false;
    input.focus();
}

async function pollMessages() {
    try {
        let url = `${FETCH_URL}?since=${lastMsgId}`;
        if (WITH_ID) url += `&with=${WITH_ID}`;
        const res  = await fetch(url, { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} });
        const msgs = await res.json();
        let appended = 0;
        msgs.forEach(m => {
            if (!document.querySelector(`[data-id="${m.id}"]`)) {
                appendMessage(m); appended++;
            }
            if (m.id > lastMsgId) lastMsgId = m.id;
        });
        if (appended > 0) scrollToBottom();
    } catch(e) { /* silent */ }
}

// Poll every 3 seconds
polling = setInterval(pollMessages, 3000);
window.addEventListener('beforeunload', () => clearInterval(polling));
input.focus();
lucide.createIcons();
</script>
@endpush
