@if(!empty($newsItems))
<div class="cm-news-grid{{ !empty($compact) ? ' cm-news-grid--compact' : '' }}">
    @foreach($newsItems as $item)
        <article class="cm-news-card cm-reveal">
            <div class="cm-news-card__meta">
                <span class="cm-news-card__source">{{ __($item['source'] ?? 'Finance') }}</span>
                @if(!empty($item['published']))
                    <time>{{ $item['published'] }}</time>
                @endif
            </div>
            <h3 class="cm-news-card__title">
                <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer">{{ __($item['title']) }}</a>
            </h3>
            @if(!empty($item['excerpt']) && empty($compact))
                <p class="cm-news-card__excerpt">{{ __($item['excerpt']) }}</p>
            @endif
            <div class="cm-news-card__foot">
                <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="cm-news-card__read">
                    @lang('Read article') <i class="las la-external-link-alt"></i>
                </a>
            </div>
        </article>
    @endforeach
</div>
@endif
