@if ($paginator->hasPages())
    <nav class="pager" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="pager__btn is-disabled" aria-disabled="true"><i class="ri-arrow-left-s-line" aria-hidden="true"></i><span>Sebelumnya</span></span>
        @else
            <a class="pager__btn" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="ri-arrow-left-s-line" aria-hidden="true"></i><span>Sebelumnya</span></a>
        @endif

        <div class="pager__nums">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pager__dots" aria-hidden="true">…</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pager__num is-current" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="pager__num" href="{{ $url }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        @if ($paginator->hasMorePages())
            <a class="pager__btn" href="{{ $paginator->nextPageUrl() }}" rel="next"><span>Berikutnya</span><i class="ri-arrow-right-s-line" aria-hidden="true"></i></a>
        @else
            <span class="pager__btn is-disabled" aria-disabled="true"><span>Berikutnya</span><i class="ri-arrow-right-s-line" aria-hidden="true"></i></span>
        @endif
    </nav>
@endif
