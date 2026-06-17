@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $policyPages = getContent('policy_pages.element', false, null, true);
        $registerContent = getContent('register.content', true);
    @endphp

    <section class="account">
        <div class="container">
            <div class="account-wrapper">
                <div class="account-wrapper-inner">
                    <div class="account-shape-one">
                        <img src="{{ asset($activeTemplateTrue . 'images/shapes/invest02.png') }}" alt="">
                    </div>
                    <div class="account-shape-two">
                        <img src="{{ asset($activeTemplateTrue . 'images/shapes/invest01.png') }}" alt="">
                    </div>
                    <div class="row justify-content-center align-items-center">
                        <div class="col-xl-6">
                            <div class="account-form">
                                <div class="account-form__content mb-4">
                                    <h2 class="account-form__title my-3"> @lang('Register') </h2>
                                    <div class="col-sm-12">
                                        <div class="have-account">
                                            <p class="have-account__text">@lang('Already Have An Account') ?
                                                <a href="{{ route('user.login') }}"
                                                    class="have-account__link text-white underline">@lang('Login Now')</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha"
                                    autocomplete="off">
                                    @csrf

                                    <div class="row">
                                        @if (session()->get('reference') != null)
                                            <div class="col-md-12 ">
                                                <h6 class="pb-4">@lang('You\'re referred by')
                                                    <i class="fw-bold text--color">{{ session()->get('reference') }}</i>
                                                </h6>
                                            </div>
                                        @endif
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="username" class="form--label">@lang('Username')</label>
                                                <input type="text" name="username" class="form--control checkUser"
                                                    id="username" value="{{ old('username') }}" required>
                                                <small class="text--danger usernameExist"></small>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="email" class="form--label">@lang('Email Address')</label>
                                                <input type="text" name="email" class="form--control checkUser"
                                                    id="email" value="{{ old('email') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="country" class="form--label">@lang('Country')</label>
                                                <select name="country" class="form--control" id="country" required>
                                                    @foreach ($countries as $key => $country)
                                                        <option data-mobile_code="{{ $country->dial_code }}"
                                                            value="{{ $country->country }}" data-code="{{ $key }}">
                                                            {{ __($country->country) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mobile" class="form--label">@lang('Mobile')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg--base mobile-code">

                                                    </span>
                                                    <input type="hidden" name="mobile_code">
                                                    <input type="hidden" name="country_code">
                                                    <input type="number" name="mobile" value="{{ old('mobile') }}"
                                                        class="form-control form--control checkUser" id="mobile"
                                                        required>
                                                    <small class="text--danger mobileExist"></small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="password" class="form--label">@lang('Password')</label>
                                                <div class="input-group">
                                                    <input id="your-password" type="password" class="form--control"
                                                        name="password" required>
                                                    <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                                        id="#password"></div>

                                                    @if ($general->secure_password)
                                                        <div class="input-popup">
                                                            <p class="error lower">@lang('1 small letter minimum')</p>
                                                            <p class="error capital">@lang('1 capital letter minimum')</p>
                                                            <p class="error number">@lang('1 number minimum')</p>
                                                            <p class="error special">@lang('1 special character minimum')</p>
                                                            <p class="error minimum">@lang('6 character password')</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="password_confirmation"
                                                    class="form--label">@lang('Confirm Password')</label>
                                                <div class="input-group">
                                                    <input id="password_confirmation" type="password" class="form--control"
                                                        name="password_confirmation" required>
                                                    <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                                        id="#password_confirmation"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <x-captcha />

                                        @if ($general->agree)
                                            <div class="form-group col-sm-12">
                                                <div class="form--check">
                                                    <div class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" id="agree"
                                                            @checked(old('agree')) name="agree" required>
                                                        <label for="agree">
                                                            @lang('I agree with') @foreach ($policyPages as $policy)
                                                                <a
                                                                    href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}">{{ __($policy->data_values->title) }}</a>
                                                                @if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-sm-12">
                                            <button type="submit" id="recaptcha"
                                                class="btn btn--base w-100">@lang('Register')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="account-thumb style-two">
                                <img src="{{ getImage('assets/images/frontend/register/' . @$registerContent->data_values->image, '455x405') }}"
                                    alt="@lang('Register Image')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="custom--modal modal fade" id="existModalCenter" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="custom--modal modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger btn--sm pill"
                        data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.login') }}" class="btn btn--base btn--sm pill">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('style')
    <style>
        .input-group-text {
            border: 0 !important;
            color: hsl(var(--white)) !important;
        }

        .underline {
            text-decoration: underline !important;
        }

        .underline:hover {
            color: hsl(var(--base)) !important;
        }
    </style>
@endpush

@if ($general->secure_password)
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif

@push('script')
    <script>
        "use strict";
        (function($) {
            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif
            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            $('.checkUser').on('focusout', function(e) {


                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    console.log(mobile);
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }

                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }

                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }

                $.post(url, data, function(response) {
                    if (response.data != false && response.type == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });


            });

           
        })(jQuery);
    </script>
@endpush
