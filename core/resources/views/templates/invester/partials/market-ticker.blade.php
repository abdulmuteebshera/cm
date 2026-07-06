@if(!empty($marketIndices))
<section class="cm-ticker" aria-label="@lang('Market indices')">
    <div class="cm-ticker__label">
        <span class="cm-ticker__live"><i class="las la-circle"></i> @lang('Markets')</span>
    </div>
    <div class="cm-ticker__track">
        <div class="cm-ticker__strip">
            @foreach(array_merge($marketIndices, $marketIndices) as $index)
                <div class="cm-ticker__item">
                    <span class="cm-ticker__name">{{ __($index['label']) }}</span>
                    <strong>{{ number_format($index['price'], 2) }}</strong>
                    <span class="cm-ticker__change cm-ticker__change--{{ $index['direction'] }}">
                        <i class="las la-arrow-{{ $index['direction'] === 'up' ? 'up' : 'down' }}"></i>
                        {{ $index['change_pct'] >= 0 ? '+' : '' }}{{ number_format($index['change_pct'], 2) }}%
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @empty($hideTickerLink)
    <a href="{{ route('markets') }}" class="cm-ticker__link">@lang('Markets') <i class="las la-arrow-right"></i></a>
    @endempty
</section>
@endif
