@extends($activeTemplate . 'layouts.app')
@section('panel')
    @php $isLanding = request()->routeIs('home', 'about', 'contact'); @endphp

    @if ($isLanding)
        @include($activeTemplate . 'partials.cm-header')
        <main class="cm-page">
            @yield('content')
        </main>
        @include($activeTemplate . 'partials.cm-footer')
    @else
        <div class="preloader"><div class="animated-preloader"></div></div>
        <div class="overlay"></div>
        <div class="header">
            <div class="container">
                <div class="header-bottom">
                    <div class="header-bottom-area align-items-center">
                        <div class="logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo.png')) }}" alt="{{ $general->site_name }}">
                            </a>
                        </div>
                        <ul class="menu ms-auto">
                            <li><a href="{{ route('home') }}">@lang('Home')</a></li>
                            @php $pages = App\Models\Page::where('tempname', $activeTemplate)->where('is_default', 0)->get(); @endphp
                            @foreach ($pages as $data)
                                <li><a href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a></li>
                            @endforeach
                            <li><a href="{{ route('contact') }}">@lang('Contact')</a></li>
                            @if (auth()->check())
                                <li class="menu-btn"><a href="{{ route('user.home') }}"><i class="las la-user"></i> @lang('Dashboard')</a></li>
                            @else
                                <li class="menu-btn"><a href="{{ route('user.login') }}"><i class="las la-user"></i> @lang('Login')</a></li>
                            @endif
                        </ul>
                        @if ($general->language_switch)
                            @php $language = App\Models\Language::all(); @endphp
                            <select name="langSel" class="langSel form--control h-auto px-2 py-1 border-0">
                                @foreach ($language as $item)
                                    <option value="{{ $item->code }}" @selected(session('lang') == $item->code)>{{ __($item->name) }}</option>
                                @endforeach
                            </select>
                        @endif
                        <div class="header-trigger-wrapper d-flex d-lg-none align-items-center">
                            <div class="header-trigger"><span></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @yield('content')
        @php $content = getContent('footer.content', true); @endphp
        <footer class="py-4">
            <div class="container">
                <div class="footer-content text-center">
                    <a href="{{ route('home') }}" class="logo mb-3">
                        <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo_2.png')) }}" alt="{{ $general->site_name }}">
                    </a>
                    <p class="footer-text mx-auto">{{ __(@$content->data_values->content) }}</p>
                    <p class="copy-right-text">&copy; {{ date('Y') }} <a href="{{ route('home') }}" class="text--base">{{ __($general->site_name) }}</a>. @lang('All Rights Reserved')</p>
                </div>
            </div>
        </footer>
    @endif
@endsection
