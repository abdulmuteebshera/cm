@php
    $policyPages   = getContent('policy_pages.element', orderById: true);
    $privacyPolicy = $policyPages->first(fn ($p) => stripos((string) ($p->data_values->title ?? ''), 'privacy') !== false);
    $termsPolicy   = $policyPages->first(fn ($p) => stripos((string) ($p->data_values->title ?? ''), 'term') !== false);
@endphp
<footer class="cm-footer">
    <div class="cm-container">
        <div class="cm-footer__grid">
            <div class="cm-footer__brand">
                <a href="{{ route('home') }}">
                    <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo_2.png')) }}" alt="Crownmaire Capital — Quantitative Asset Management">
                </a>
                <p>@lang('Crownmaire Capital is a quantitative asset management firm serving qualified investors from New York and Dubai. We manage invitation-only multi-asset programs with institutional reporting and disciplined risk controls.')</p>
            </div>
            <div>
                <h5 class="cm-footer__title">@lang('Company')</h5>
                <ul class="cm-footer__links">
                    <li><a href="{{ route('home') }}">@lang('Home')</a></li>
                    <li><a href="{{ route('about') }}">@lang('About')</a></li>
                    <li><a href="{{ route('strategies') }}">@lang('Strategies')</a></li>
                    <li><a href="{{ route('platform') }}">@lang('Platform')</a></li>
                    <li><a href="{{ route('careers') }}">@lang('Careers')</a></li>
                    <li><a href="{{ route('contact') }}">@lang('Contact')</a></li>
                </ul>
            </div>
            <div>
                <h5 class="cm-footer__title">@lang('Resources')</h5>
                <ul class="cm-footer__links">
                    <li><a href="{{ route('markets') }}">@lang('Markets & News')</a></li>
                    <li><a href="{{ route('home') }}#faqs">@lang('FAQs')</a></li>
                    <li><a href="{{ route('plan') }}">@lang('Programs')</a></li>
                    @if ($privacyPolicy)
                        <li><a href="{{ route('policy.pages', [slug($privacyPolicy->data_values->title), $privacyPolicy->id]) }}">@lang('Privacy Policy')</a></li>
                    @endif
                    @if ($termsPolicy)
                        <li><a href="{{ route('policy.pages', [slug($termsPolicy->data_values->title), $termsPolicy->id]) }}">@lang('Terms and Conditions')</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <h5 class="cm-footer__title">@lang('Portal')</h5>
                <ul class="cm-footer__links">
                    <li>100 Wall Street Ct, New York, NY 10005</li>
                    <li>2402 Al-Manara Tower, Business Bay, Dubai</li>
                    <li><a href="tel:+19175006476">+1 917 500 6476</a></li>
                    <li><a href="mailto:Info@crownmaire.com">Info@crownmaire.com</a></li>
                    <li><a href="{{ route('user.login') }}">@lang('Member Login')</a></li>
                    <li><a href="{{ route('contact') }}">@lang('Request Invitation')</a></li>
                </ul>
            </div>
        </div>
        @if ($privacyPolicy || $termsPolicy)
            <nav class="cm-footer__legal" aria-label="@lang('Legal')">
                @if ($privacyPolicy)
                    <a href="{{ route('policy.pages', [slug($privacyPolicy->data_values->title), $privacyPolicy->id]) }}">@lang('Privacy Policy')</a>
                @endif
                @if ($privacyPolicy && $termsPolicy)
                    <span aria-hidden="true">·</span>
                @endif
                @if ($termsPolicy)
                    <a href="{{ route('policy.pages', [slug($termsPolicy->data_values->title), $termsPolicy->id]) }}">@lang('Terms and Conditions')</a>
                @endif
            </nav>
        @endif
        <p class="cm-footer__copy">&copy; {{ date('Y') }} <a href="{{ route('home') }}">{{ __($general->site_name) }}</a>. @lang('All Rights Reserved')</p>
    </div>
</footer>
