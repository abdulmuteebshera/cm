@if(!empty($newsTotalPages) && $newsTotalPages > 1)
<nav class="cm-markets-pagination cm-reveal" aria-label="@lang('News pages')">
    <ul class="cm-markets-pagination__list">
        @if($newsCurrentPage > 1)
            <li>
                <a href="{{ route('markets', ['page' => $newsCurrentPage - 1]) }}" class="cm-markets-pagination__btn" aria-label="@lang('Previous page')">
                    <i class="las la-angle-left"></i>
                </a>
            </li>
        @endif
        @for($p = 1; $p <= $newsTotalPages; $p++)
            <li>
                <a href="{{ route('markets', ['page' => $p]) }}"
                   class="cm-markets-pagination__num{{ $p == $newsCurrentPage ? ' is-active' : '' }}">
                    {{ $p }}
                </a>
            </li>
        @endfor
        @if($newsCurrentPage < $newsTotalPages)
            <li>
                <a href="{{ route('markets', ['page' => $newsCurrentPage + 1]) }}" class="cm-markets-pagination__btn" aria-label="@lang('Next page')">
                    <i class="las la-angle-right"></i>
                </a>
            </li>
        @endif
    </ul>
</nav>
@endif
