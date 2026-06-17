<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand logo" href="{{ route('home') }}"><img
                    src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt=""></a>
            <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav nav-menu ms-auto ">
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('home') }}" href="{{ route('home') }}">@lang('Home')</a>
                    </li>
                    @php
                        $pages = App\Models\Page::where('tempname', $activeTemplate)
                            ->where('is_default', 0)
                            ->get();
                    @endphp
                    @foreach ($pages as $data)
                        <li class="nav-item"><a href="{{ route('pages', [$data->slug]) }}"
                                class="nav-link {{ menuActive('pages', [$data->slug]) }}">{{ __($data->name) }}</a></li>
                    @endforeach
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('plan') }}" href="{{ route('plan') }}"> @lang('Plan') </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('blogs') }}" href="{{ route('blogs') }}">@lang('Blog')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('contact') }}"
                            href="{{ route('contact') }}">@lang('Contact')</a>
                    </li>
                    <!-- languages -->
                    @if ($general->language_switch)
                        <li class="nav-item">
                            <div class="language-select d-flex flex-wrap align-items-center">
                                <div class="language-box">
                                    <select class="select langSel">
                                        @foreach ($language as $item)
                                            <option value="{{ $item->code }}"
                                                @if (session('lang') == $item->code) selected @endif>{{ __($item->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </li>
                    @endif
                </ul>
                <ul class="login-registration-list d-flex flex-wrap align-items-center">
                    @auth
                        <li class="login-registration-list__item"><a href="{{ route('user.home') }}"
                                class="login-registration-list__link btn btn--base btn--sm"> @lang('Dashboard')</a></li>
                        <li class="login-registration-list__item"><a href="{{ route('user.logout') }}"
                                class="login-registration-list__link btn btn--base btn--sm"> @lang('Logout')</a></li>
                    @else
                        <li class="login-registration-list__item"><a href="{{ route('user.register') }}"
                                class="login-registration-list__link btn btn--base btn--sm"> @lang('Register')</a></li>
                        <li class="login-registration-list__item"><a href="{{ route('user.login') }}"
                                class="login-registration-list__link btn btn--base btn--sm"> @lang('Login')</a></li>
                    @endauth
                </ul>
            </div>
        </nav>
    </div>
</header>
