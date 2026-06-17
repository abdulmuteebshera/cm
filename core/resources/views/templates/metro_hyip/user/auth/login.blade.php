@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $loginContent = getContent('login.content', true);
    @endphp

    <section class="account ">
        <div class="container">
            <div class="account-wrapper">
                <div class="account-wrapper-inner">
                    <div class="account-shape-one">
                        <img src="{{ asset($activeTemplateTrue . 'images/shapes/invest02.png') }}" alt="">
                    </div>
                    <div class="account-shape-two">
                        <img src="{{ asset($activeTemplateTrue . 'images/shapes/invest01.png') }}" alt="">
                    </div>
                    <div class="row gy-4 justify-content-center align-items-center">
                        <div class="col-xl-6">
                            <div class="account-form">
                                <div class="account-form__content mb-4">
                                    <h2 class="account-form__title mb-2"> @lang('Login') </h2>
                                    <div class="col-sm-12">
                                        <div class="have-account">
                                            <p class="have-account__text">@lang("Don't Have An Account") ?
                                                <a href="{{ route('user.register') }}"
                                                    class="have-account__link text-white underline">@lang('Create Account')</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('user.login') }}" method="POST" class="verify-gcaptcha">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label for="username" class="form--label">@lang('Username or Email')</label>
                                            <div class="form-group">
                                                <input type="text" class="form--control" id="username" name="username"
                                                    value="{{ old('username') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="password" class="form--label">@lang('Password')</label>
                                            <div class="form-group">
                                                <input id="password" type="password" name="password" class="form--control">
                                                <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                                    id="#password"></div>
                                            </div>
                                        </div>

                                        <x-captcha />

                                        <div class="form-group col-sm-12">
                                            <div class="d-flex flex-wrap justify-content-between">
                                                <div class="form--check">
                                                    <input class="form-check-input" type="checkbox" name="remember"
                                                        id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="form-check-label w-auto" for="remember">@lang('Remember me')
                                                    </label>
                                                </div>
                                                <a href="{{ route('user.password.request') }}"
                                                    class="forgot-password">@lang('Forgot Your Password') ?</a>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <button type="submit" id="recaptcha"
                                                class="btn btn--base w-100">@lang('Login')</button>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="account-thumb">
                                <img src="{{ getImage('assets/images/frontend/login/' . @$loginContent->data_values->image, '455x405') }}"
                                    alt="@lang('Register Image')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('style')
    <style>
        .underline {
            text-decoration: underline !important;
        }

        .underline:hover {
            color: hsl(var(--base)) !important;
        }
    </style>
@endpush
