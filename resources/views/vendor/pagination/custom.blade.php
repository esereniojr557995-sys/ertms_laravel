@if ($paginator->hasPages())
<div class="pagination">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="padding:6px 11px;color:var(--border);font-size:.78rem">‹</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}">‹</a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:6px 4px;color:var(--text-muted);font-size:.78rem">…</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}">›</a>
    @else
        <span style="padding:6px 11px;color:var(--border);font-size:.78rem">›</span>
    @endif
</div>
@endif
