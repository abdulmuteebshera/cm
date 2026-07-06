@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="cm-page-hero cm-page-hero--compact">
        <div class="cm-page-hero__aurora" aria-hidden="true"></div>
        <div class="cm-page-hero__grid" aria-hidden="true"></div>
        <div class="cm-container cm-page-hero__inner cm-reveal">
            <span class="cm-badge"><i class="las la-file-contract"></i> @lang('Legal')</span>
            <h1 class="cm-page-hero__title">{{ __($pageTitle) }}</h1>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-container">
            <div class="cm-legal-content cm-reveal">
                {!! $policy->data_values->details !!}
            </div>
        </div>
    </section>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/crownmaire-landing.css') }}?v=28">
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/crownmaire-landing.js') }}?v=28" defer></script>
@endpush
