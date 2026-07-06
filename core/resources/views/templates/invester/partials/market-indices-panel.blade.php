@if(!empty($marketIndices))
<div class="cm-markets-indices cm-reveal">
    <div class="cm-markets-indices__head">
        <span class="cm-markets-indices__live"><i class="las la-circle"></i> @lang('Live indices')</span>
        <span class="cm-markets-indices__updated">@lang('Updated every 10 minutes')</span>
    </div>
    <div class="cm-markets-indices__grid">
        @foreach($marketIndices as $index)
            <div class="cm-markets-index">
                <span class="cm-markets-index__label">{{ __($index['label']) }}</span>
                <strong class="cm-markets-index__price">{{ number_format($index['price'], 2) }}</strong>
                <span class="cm-markets-index__change cm-markets-index__change--{{ $index['direction'] }}">
                    <i class="las la-arrow-{{ $index['direction'] === 'up' ? 'up' : 'down' }}"></i>
                    {{ $index['change_pct'] >= 0 ? '+' : '' }}{{ number_format($index['change_pct'], 2) }}%
                </span>
            </div>
        @endforeach
    </div>
</div>
@endif
