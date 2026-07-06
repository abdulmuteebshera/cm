@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="cm-page-hero cm-page-hero--compact">
        <div class="cm-page-hero__aurora" aria-hidden="true"></div>
        <div class="cm-page-hero__grid" aria-hidden="true"></div>
        <div class="cm-container cm-page-hero__inner cm-reveal">
            <span class="cm-badge"><i class="las la-file-upload"></i> @lang('Job Application')</span>
            <h1 class="cm-page-hero__title">{{ __($jobPost->title) }}</h1>
            <p class="cm-page-hero__lead">
                @if ($jobPost->department || $jobPost->location)
                    {{ collect([$jobPost->department, $jobPost->location, $jobPost->employment_type])->filter()->implode(' · ') }}
                @else
                    @lang('Submit your details and résumé for this role.')
                @endif
            </p>
            <a href="{{ route('careers') }}" class="cm-btn cm-btn--ghost cm-btn--on-dark cm-btn--sm">
                <i class="las la-arrow-left"></i> @lang('Back to Careers')
            </a>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-container">
            <div class="cm-career-apply-layout">
                <aside class="cm-career-apply-info cm-reveal">
                    <span class="cm-section__tag">@lang('Role summary')</span>
                    <h2 class="cm-split__title">{{ __($jobPost->title) }}</h2>
                    @if ($jobPost->summary)
                        <p class="cm-career-apply-info__text">{{ __($jobPost->summary) }}</p>
                    @endif
                    <ul class="cm-career-apply-meta">
                        @if ($jobPost->department)
                            <li><i class="las la-building"></i> {{ __($jobPost->department) }}</li>
                        @endif
                        @if ($jobPost->location)
                            <li><i class="las la-map-marker"></i> {{ __($jobPost->location) }}</li>
                        @endif
                        @if ($jobPost->employment_type)
                            <li><i class="las la-clock"></i> {{ __($jobPost->employment_type) }}</li>
                        @endif
                    </ul>
                </aside>

                <div class="cm-contact-form-wrap cm-career-apply-form cm-reveal cm-reveal--delay">
                    <div class="cm-career-apply-notice">
                        <i class="las la-info-circle"></i>
                        <p>@lang('No login or member account required — anyone may apply for this position.')</p>
                    </div>
                    <form action="{{ route('careers.apply.submit', $jobPost->id) }}" class="cm-contact-form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="cm-form-row cm-form-row--2">
                            <div class="cm-form-group">
                                <label for="apply_name">@lang('Full Name')</label>
                                <input type="text" id="apply_name" name="name" class="cm-form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="cm-form-group">
                                <label for="apply_email">@lang('Email')</label>
                                <input type="email" id="apply_email" name="email" class="cm-form-control" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="cm-form-row cm-form-row--2">
                            <div class="cm-form-group">
                                <label for="apply_phone">@lang('Phone')</label>
                                <input type="text" id="apply_phone" name="phone" class="cm-form-control" value="{{ old('phone') }}">
                            </div>
                            <div class="cm-form-group">
                                <label for="apply_linkedin">@lang('LinkedIn Profile')</label>
                                <input type="url" id="apply_linkedin" name="linkedin" class="cm-form-control" value="{{ old('linkedin') }}" placeholder="https://linkedin.com/in/...">
                            </div>
                        </div>
                        <div class="cm-form-group">
                            <label for="apply_message">@lang('Cover Letter / Message')</label>
                            <textarea id="apply_message" name="message" class="cm-form-control" rows="6" placeholder="@lang('Tell us about your experience and why you are a fit for this role...')" required>{{ old('message') }}</textarea>
                        </div>
                        <div class="cm-form-group">
                            <label for="apply_resume">@lang('Résumé / CV')</label>
                            <input type="file" id="apply_resume" name="resume" class="cm-form-control cm-form-file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                            <small class="cm-form-hint">@lang('PDF or Word document, max 5 MB')</small>
                        </div>
                        <div class="cm-form-footer">
                            <button type="submit" class="cm-btn cm-btn--accent cm-form-footer__submit">
                                <i class="las la-paper-plane"></i> @lang('Submit Application')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/crownmaire-landing.css') }}?v=27">
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/crownmaire-landing.js') }}?v=27" defer></script>
@endpush
