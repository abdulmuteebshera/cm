<header class="cm-header" id="cmHeader">
    <div class="cm-header__bar">
        <a href="{{ route('home') }}" class="cm-header__brand">
            <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo.png')) }}" alt="{{ $general->site_name }}">
        </a>
        <nav class="cm-header__nav" aria-label="Main">
            <button type="button" class="cm-header__toggle" id="cmNavToggle" aria-expanded="false" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <ul class="cm-header__menu" id="cmNavMenu">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">@lang('Home')</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'is-active' : '' }}">@lang('About')</a></li>
                <li><a href="{{ route('home') }}#dashboard">@lang('Platform')</a></li>
                <li><a href="{{ route('home') }}#faqs">@lang('FAQs')</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'is-active' : '' }}">@lang('Contact')</a></li>
                @if (auth()->check())
                    <li class="cm-header__menu-portal">
                        <a href="{{ route('user.home') }}"><i class="las la-chart-line"></i> @lang('Dashboard')</a>
                    </li>
                @else
                    <li class="cm-header__menu-portal">
                        <a href="{{ route('user.login') }}"><i class="las la-sign-in-alt"></i> @lang('Login')</a>
                    </li>
                @endif
            </ul>
        </nav>
        <div class="cm-header__actions">
            @if (auth()->check())
                <a href="{{ route('user.home') }}" class="cm-header__portal">
                    <i class="las la-th-large"></i>
                    <span>@lang('Dashboard')</span>
                </a>
            @else
                <a href="{{ route('user.login') }}" class="cm-header__portal cm-header__portal--ghost">
                    <i class="las la-user"></i>
                    <span>@lang('Login')</span>
                </a>
            @endif
            <a href="{{ route('contact') }}" class="cm-btn cm-btn--sm cm-btn--accent">
                @lang('Request Invitation')
            </a>
        </div>
    </div>
</header>
