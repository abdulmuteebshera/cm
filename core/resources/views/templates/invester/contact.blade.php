@extends($activeTemplate . 'layouts.frontend')
@section('content')
    {{-- Page hero --}}
    <section class="cm-page-hero">
        <div class="cm-page-hero__aurora" aria-hidden="true"></div>
        <div class="cm-page-hero__grid" aria-hidden="true"></div>
        <div class="cm-container cm-page-hero__inner cm-reveal">
            <span class="cm-badge"><i class="las la-envelope"></i> @lang('Contact us')</span>
            <h1 class="cm-page-hero__title">@lang('We welcome serious inquiries from eligible investors and institutions')</h1>
            <p class="cm-page-hero__lead">@lang('Request a private consultation, invitation, or dashboard preview.')</p>
        </div>
    </section>

    {{-- Contact form --}}
    <section class="cm-section">
        <div class="cm-container">
            <div class="cm-contact-layout">
                <div class="cm-contact-info cm-reveal">
                    <span class="cm-section__tag">@lang('Get in touch')</span>
                    <h2 class="cm-split__title">@lang('Speak with our team')</h2>
                    <p class="cm-contact-info__text">@lang('Crownmaire Capital responds to qualified inquiries regarding private investment programs, platform access, and institutional partnerships.')</p>

                    <ul class="cm-contact-details">
                        <li>
                            <i class="las la-phone"></i>
                            <div>
                                <span>@lang('Phone')</span>
                                <a href="tel:+19175006476">+1 917 500 6476</a>
                            </div>
                        </li>
                        <li>
                            <i class="las la-envelope"></i>
                            <div>
                                <span>@lang('Email')</span>
                                <a href="mailto:Info@crownmaire.com">Info@crownmaire.com</a>
                            </div>
                        </li>
                        <li>
                            <i class="las la-map-marker"></i>
                            <div>
                                <span>@lang('New York')</span>
                                <p>100 Wall Street Ct, New York, NY 10005</p>
                            </div>
                        </li>
                        <li>
                            <i class="las la-map-marker"></i>
                            <div>
                                <span>@lang('Dubai')</span>
                                <p>2402 Al-Manara Tower, Business Bay, Dubai</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="cm-contact-form-wrap cm-reveal cm-reveal--delay">
                    <form action="{{ route('contact') }}" class="cm-contact-form verify-gcaptcha" method="post">
                        @csrf
                        <div class="cm-form-row cm-form-row--2">
                            <div class="cm-form-group">
                                <label for="contact_name">@lang('Name')</label>
                                <input
                                    type="text"
                                    id="contact_name"
                                    name="name"
                                    class="cm-form-control"
                                    value="{{ old('name', @$user->fullname) }}"
                                    @if ($user) readonly @endif
                                    required
                                >
                            </div>
                            <div class="cm-form-group">
                                <label for="contact_company">@lang('Company name')</label>
                                <input
                                    type="text"
                                    id="contact_company"
                                    name="company_name"
                                    class="cm-form-control"
                                    value="{{ old('company_name') }}"
                                >
                            </div>
                        </div>
                        <div class="cm-form-row cm-form-row--2">
                            <div class="cm-form-group">
                                <label for="contact_email">@lang('Email')</label>
                                <input
                                    type="email"
                                    id="contact_email"
                                    name="email"
                                    class="cm-form-control"
                                    value="{{ old('email', @$user->email) }}"
                                    @if ($user) readonly @endif
                                    required
                                >
                            </div>
                            <div class="cm-form-group">
                                <label for="contact_phone">@lang('Phone')</label>
                                <input
                                    type="text"
                                    id="contact_phone"
                                    name="phone"
                                    class="cm-form-control"
                                    value="{{ old('phone') }}"
                                >
                            </div>
                        </div>
                        <div class="cm-form-group">
                            <label for="contact_reason">@lang('Select Reason for Contact')</label>
                            <select id="contact_reason" name="subject" class="cm-form-control" required>
                                <option value="" disabled @selected(!old('subject'))>@lang('Select reason')</option>
                                <option value="Investment" @selected(old('subject') === 'Investment')>@lang('Investment')</option>
                                <option value="Others" @selected(old('subject') === 'Others')>@lang('Others')</option>
                            </select>
                        </div>
                        <div class="cm-form-group">
                            <label for="contact_message">@lang('Message')</label>
                            <textarea
                                id="contact_message"
                                name="message"
                                class="cm-form-control"
                                rows="5"
                                placeholder="@lang('Write down your message')..."
                                required
                            >{{ old('message') }}</textarea>
                        </div>
                        <div class="cm-form-footer">
                            <div class="cm-form-footer__captcha">
                                <x-captcha path="templates.invester.partials" />
                            </div>
                            <button type="submit" class="cm-btn cm-btn--accent cm-form-footer__submit">
                                <i class="las la-paper-plane"></i> @lang('Send')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cm-section cm-section--cta">
        <div class="cm-container">
            <div class="cm-banner cm-reveal">
                <div class="cm-banner__pattern" aria-hidden="true"></div>
                <div class="cm-banner__overlay"></div>
                <div class="cm-banner__content">
                    <div class="cm-banner__icon"><i class="las la-rocket"></i></div>
                    <h3>@lang('Access the future of smart investment strategies')</h3>
                    <p>@lang('Connect with our team to learn how Crownmaire\'s quantitative programs align with your capital objectives.')</p>
                    <div class="cm-banner__actions">
                        <a href="{{ route('contact') }}" class="cm-btn cm-btn--accent">
                            <i class="las la-envelope"></i> @lang('Contact experts')
                        </a>
                        @if (auth()->check())
                            <a href="{{ route('user.home') }}" class="cm-btn cm-btn--ghost cm-btn--on-dark">
                                <i class="las la-th-large"></i> @lang('Dashboard')
                            </a>
                        @else
                            <a href="{{ route('user.login') }}" class="cm-btn cm-btn--ghost cm-btn--on-dark">
                                <i class="las la-sign-in-alt"></i> @lang('Member Login')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style-lib')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/crownmaire-landing.css') }}?v=12">
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/crownmaire-landing.js') }}?v=12"></script>
@endpush
