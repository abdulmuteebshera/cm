<div class="cm-careers-list cm-reveal">
    @foreach ($jobs as $i => $job)
        <article class="cm-careers-card{{ $i > 0 ? ' cm-reveal--delay' . min($i, 3) : '' }}" id="job-{{ $job->id }}">
            <header class="cm-careers-card__head">
                <div class="cm-careers-card__meta">
                    @if ($job->department)
                        <span><i class="las la-building"></i> {{ __($job->department) }}</span>
                    @endif
                    @if ($job->location)
                        <span><i class="las la-map-marker"></i> {{ __($job->location) }}</span>
                    @endif
                    @if ($job->employment_type)
                        <span><i class="las la-clock"></i> {{ __($job->employment_type) }}</span>
                    @endif
                    <span><i class="las la-calendar"></i> {{ showDateTime($job->created_at, 'd M Y') }}</span>
                </div>
                <h3 class="cm-careers-card__title">{{ __($job->title) }}</h3>
                @if ($job->summary)
                    <p class="cm-careers-card__summary">{{ __($job->summary) }}</p>
                @endif
            </header>

            <div class="cm-careers-card__body">
                <div class="cm-careers-card__block">
                    <h4>@lang('Role overview')</h4>
                    <div class="cm-careers-card__text">{!! nl2br(e($job->description)) !!}</div>
                </div>
                @if ($job->requirements)
                    <div class="cm-careers-card__block">
                        <h4>@lang('Requirements')</h4>
                        <div class="cm-careers-card__text">{!! nl2br(e($job->requirements)) !!}</div>
                    </div>
                @endif
            </div>

            <footer class="cm-careers-card__foot">
                <a href="{{ route('careers.apply', $job->id) }}" class="cm-btn cm-btn--accent cm-btn--sm">
                    <i class="las la-paper-plane"></i> @lang('Apply Now')
                </a>
            </footer>
        </article>
    @endforeach
</div>
