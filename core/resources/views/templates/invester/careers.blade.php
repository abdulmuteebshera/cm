@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="cm-page-hero">
        <div class="cm-page-hero__aurora" aria-hidden="true"></div>
        <div class="cm-page-hero__grid" aria-hidden="true"></div>
        <div class="cm-container cm-page-hero__inner cm-reveal">
            <span class="cm-badge"><i class="las la-briefcase"></i> @lang('Careers')</span>
            <h1 class="cm-page-hero__title">@lang('Build the Future of Quantitative Asset Management')</h1>
            <p class="cm-page-hero__lead">@lang('Join Crownmaire Capital and work at the intersection of data science, algorithmic trading, and institutional portfolio management.')</p>
        </div>
    </section>

    <section class="cm-section cm-section--careers">
        <div class="cm-container">
            <div class="cm-section__header cm-reveal">
                <span class="cm-section__tag">@lang('Open positions')</span>
                <h2 class="cm-section__title">@lang('Current Opportunities')</h2>
                <p class="cm-section__lead">@lang('Explore active roles across research, technology, operations, and client services. Only published openings appear here.')</p>
            </div>

            @if ($jobPosts->isNotEmpty())
                @include($activeTemplate . 'partials.career-jobs-list', ['jobs' => $jobPosts])
            @else
                <div class="cm-careers-empty cm-reveal">
                    <div class="cm-careers-empty__icon" aria-hidden="true"><i class="las la-briefcase"></i></div>
                    <h3>@lang('No open positions at the moment')</h3>
                    <p>@lang('We are not actively hiring for listed roles right now. You may still reach out with your background — we welcome exceptional talent for future opportunities.')</p>
                    <a href="{{ route('contact') }}" class="cm-btn cm-btn--accent"><i class="las la-paper-plane"></i> @lang('Contact Us')</a>
                </div>
            @endif
        </div>
    </section>

    <section class="cm-section cm-section--alt">
        <div class="cm-container">
            <div class="cm-banner cm-reveal">
                <div class="cm-banner__pattern" aria-hidden="true"></div>
                <div class="cm-banner__overlay"></div>
                <div class="cm-banner__content">
                    <div class="cm-banner__icon"><i class="las la-envelope"></i></div>
                    <h3>@lang('Interested in joining Crownmaire?')</h3>
                    <p>@lang('Send your résumé and a brief note on the role you are pursuing. Our team reviews qualified candidates on an ongoing basis.')</p>
                    <div class="cm-banner__actions">
                        <a href="{{ route('contact') }}" class="cm-btn cm-btn--accent cm-btn--glow"><i class="las la-paper-plane"></i> @lang('Get in Touch')</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/crownmaire-landing.css') }}?v=26">
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/crownmaire-landing.js') }}?v=26" defer></script>
@endpush
