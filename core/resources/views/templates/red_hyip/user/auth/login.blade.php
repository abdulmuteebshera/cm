@extends($activeTemplate . 'layouts.app')
@section('panel')
    @php
        $loginContent = getContent('login.content', true);
    @endphp

    <section class="account py-120">
        <div class="account__bg"></div>
        <div class="account__bg-shape"></div>
        <div class="account-inner">
            <div class="container">
                <div class="row gy-4 py-60">
                    <div class="col-xl-5 col-lg-6">
                        <div class="account-form">
                            <a href="{{ route('home')}}" class="account-form__icon" title="@lang('Home')"><i class="fas fa-home"></i></a>
                            <div class="account-form__content mb-4">
                                <h3 class="account-form__title mb-2"> @lang('Login Account') </h3>
                            </div>
                            <form action="{{ route('user.login') }}" method="POST"
                                class="signup-form primary-bg verify-gcaptcha">
                                @csrf
                                <div class="row gy-lg-4 gy-3">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="Username" class="form--label"> @lang('Username or Email')</label>
                                            <input type="text" name="username" class="form--control" id="Username"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="password" class="form--label">@lang('Password')</label>
                                            <div class="input-group">
                                                <input id="password" type="password" class="form--control"
                                                    name="password" required>
                                                <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                                    id="password"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="d-flex flex-wrap justify-content-between">
                                            <div class="form--check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="remember">
                                                <label class="form-check-label" for="remember">@lang('Remember me') </label>
                                            </div>
                                            <a href="{{ route('user.password.request') }}"
                                                class="forgot-password text--base">@lang('Forgot Your Password')?</a>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="captcha-img">
                                            <x-captcha />
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn--base w-100">@lang('Login')</button>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="have-account text-center">
                                            <p class="have-account__text">@lang('Don\'t Have An Account')? <a
                                                    href="{{ route('user.register') }}"
                                                    class="have-account__link text--base">@lang('Create Account')</a></p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-6">
                        <div class="account-thumb">
                            <img src="{{ getImage('assets/images/frontend/login/' . @$loginContent->data_values->image,'550x560') }}"
                                alt="Login Image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
