@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="cm-page-hero">
        <div class="cm-page-hero__aurora" aria-hidden="true"></div>
        <div class="cm-page-hero__grid" aria-hidden="true"></div>
        <div class="cm-container cm-page-hero__inner cm-reveal">
            <span class="cm-badge"><i class="las la-paper-plane"></i> @lang('Investment inquiry')</span>
            <h1 class="cm-page-hero__title">@lang('Inquire about investing with Crownmaire Capital')</h1>
            <p class="cm-page-hero__lead">@lang('Send your details and our team will contact you about private quantitative programs. Offices in New York and Dubai.')</p>
        </div>
    </section>
    @include($activeTemplate . 'partials.cm-breadcrumb')
    @include($activeTemplate . 'partials.cm-invest-inquiry', ['showOffices' => true])
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/crownmaire-landing.css') }}?v=29">
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/crownmaire-landing.js') }}?v=29" defer></script>
@endpush
