@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="cm-page-hero">
        <div class="cm-page-hero__aurora" aria-hidden="true"></div>
        <div class="cm-page-hero__grid" aria-hidden="true"></div>
        <div class="cm-container cm-page-hero__inner cm-reveal">
            <span class="cm-badge"><i class="las la-th-large"></i> @lang('Investor Platform')</span>
            <h1 class="cm-page-hero__title">@lang('An institutional-grade portal built for Crownmaire members')</h1>
            <p class="cm-page-hero__lead">@lang('Every module below is part of the proprietary Crownmaire member portal — a quant-powered command center for capital tracking, performance analytics, treasury operations, and secure investor communication.')</p>
        </div>
    </section>

    <section class="cm-section cm-section--platform">
        <div class="cm-container cm-container--platform">
            @include($activeTemplate . 'partials.platform-showcase')
        </div>
    </section>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/crownmaire-landing.css') }}?v=24">
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/crownmaire-landing.js') }}?v=24" defer></script>
@endpush
