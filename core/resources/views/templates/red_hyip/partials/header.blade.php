<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand logo" href="{{ route('home') }}">
                <img src="{{ getImage('assets/images/logoIcon/logo.png') }}" alt=" @lang('Logo')">
            </a>
            <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="header-account d-lg-none d-block">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <ul class="login-registration-list d-flex flex-wrap justify-content-between align-items-center">
                            @guest
                                <li class="login-registration-list__item"><span class="login-registration-list__icon"><i
                                            class="fas fa-user"></i></span><a href="{{ route('user.login') }}"
                                        class="login-registration-list__link"> @lang('Login')</a></li>
                                <li class="login-registration-list__item"><a href="{{ route('user.login') }}"
                                        class="login-registration-list__link"> @lang('Register')</a></li>
                            @else
                                <li class="login-registration-list__item"><a href="{{ route('user.home') }}" class="login-registration-list__link"> @lang('Dashboard')</a></li>
                            @endguest
                        </ul>
                        
                    </div>
                </div>
                <ul class="navbar-nav nav-menu ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('home') }}"
                            href="{{ route('home') }}">@lang('Home')</a>
                    </li>
                    @php
                        $pages = App\Models\Page::where('tempname', $activeTemplate)
                            ->where('is_default', 0)
                            ->get();
                    @endphp
                    @if (@$pages)
                        @foreach ($pages as $data)
                            <li class="nav-item">
                                <a class="nav-link {{ menuActive('pages', null, $data->slug) }}" href="{{ route('pages', [$data->slug]) }}"> {{ __($data->name) }}</a>
                            </li>
                        @endforeach
                    @endif

                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('plan') }}" href="{{ route('plan') }}">@lang('Plans')</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('blogs') }}"
                            href="{{ route('blogs') }}">@lang('Blogs')</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ menuActive('ticket.open') }}"
                                href="{{ route('ticket.open') }}">@lang('Support')</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ menuActive('contact') }}"
                                href="{{ route('contact') }}">@lang('Contact')</a>
                        </li>
                    @endauth
                </ul>

                <div class="header-account d-lg-block d-none">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <ul class="login-registration-list d-flex flex-wrap justify-content-between align-items-center">
                            @guest
                                <li class="login-registration-list__item"><span class="login-registration-list__icon"><i class="fas fa-user"></i></span>
                                    <a href="{{ route('user.login') }}" class="login-registration-list__link">
                                        @lang('Login')</a>
                                </li>

                                <li class="login-registration-list__item"><a href="{{ route('user.register') }}" class="login-registration-list__link"> @lang('Register')</a></li>
                            @else

                            <li class="login-registration-list__item"><span class="login-registration-list__icon"><i class="fas fa-user"></i></span>
                                <a href="{{ route('user.home') }}" class="login-registration-list__link">
                                    @lang('Dashboard')</a>
                            </li>

                            <li class="login-registration-list__item"><a href="{{ route('user.logout') }}" class="login-registration-list__link"> @lang('logout')</a></li>

                            @endguest
                        </ul>
                        @if ($general->language_switch)
                            <div class="language-box">
                                <div class="language-box__icon login-registration-list__icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <select class="select langSel">
                                    @foreach ($language as $item)
                                        <option value="{{ $item->code }}"
                                            @if (session('lang') == $item->code) selected @endif>
                                            {{ __($item->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>
