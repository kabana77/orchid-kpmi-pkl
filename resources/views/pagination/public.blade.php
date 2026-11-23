@if ($paginator->hasPages())
    <nav class="d-flex flex-wrap justify-content-center gap-1" role="navigation" aria-label="Halaman">
        @if ($paginator->onFirstPage())
            <span class="kp-pg disabled" aria-disabled="true">Sebelumnya</span>
        @else
            <a class="kp-pg" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="kp-pg disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="kp-pg on" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="kp-pg" href="{{ $url }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="kp-pg" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
        @else
            <span class="kp-pg disabled" aria-disabled="true">Berikutnya</span>
        @endif
    </nav>
@endif
