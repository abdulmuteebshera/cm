@if(!empty($newsItems))
<div class="cm-markets-feed cm-reveal">
    <div class="cm-markets-feed__head">
        <h2>@lang('Market Headlines')</h2>
    </div>
    <div class="cm-markets-feed__list">
        @foreach($newsItems as $item)
            <article class="cm-markets-feed__card">
                <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="cm-markets-feed__link">
                    <div class="cm-markets-feed__top">
                        <span class="cm-markets-feed__source">{{ __($item['source'] ?? 'Finance') }}</span>
                        @if(!empty($item['published']))
                            <time class="cm-markets-feed__date">{{ $item['published'] }}</time>
                        @endif
                    </div>
                    <h3 class="cm-markets-feed__title">{{ __($item['title']) }}</h3>
                    @if(!empty($item['excerpt']))
                        <p class="cm-markets-feed__excerpt">{{ __($item['excerpt']) }}</p>
                    @endif
                    <span class="cm-markets-feed__read">@lang('Read full story') <i class="las la-arrow-right"></i></span>
                </a>
            </article>
        @endforeach
    </div>
</div>
@endif
