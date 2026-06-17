@extends($activeTemplate . 'layouts.app')
@section('panel')
    @php
        $policyPages = getContent('policy_pages.element', orderById: true);
        $registerContent = getContent('register.content', true);

    @endphp

    <section class="account py-120">
        <div class="account__bg"></div>
        <div class="account__bg-shape"></div>
        <div class="account-inner">
            <div class="container py-60">
                <div class="row gy-4">
                    <div class="col-xl-6 col-lg-7">
                        <div class="account-form">
                            <a href="{{ route('home') }}" class="account-form__icon" title="@lang('Home')"><i
                                    class="fas fa-home"></i></a>
                            <div class="account-form__content mb-4">
                                <h3 class="account-form__title mb-2"> @lang('Register Account') </h3>
                            </div>
                            <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha">
                                @csrf

                                <div class="row gy-lg-4 gy-3">
                                    @if (session()->get('reference') != null)
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="referenceBy" class="form--label">@lang('Reference By') </label>
                                                <input type="text" name="referBy" id="referenceBy"
                                                    class="form-control form--control"
                                                    value="{{ session()->get('reference') }}" readonly>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="username" class="form--label">@lang('Username') </label>
                                            <input type="text" class="form--control checkUser" name="username"
                                                value="{{ old('username') }}" id="username" required>
                                            <small class="text-danger usernameExist"></small>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email" class="form--label">@lang('Email Address')</label>
                                            <input type="email" class="form--control checkUser" name="email"
                                                value="{{ old('email') }}" id="email" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group  has-icon-select">
                                            <label for="country" class="form-label">@lang('Country')</label>
                                            <select name="country" class="select" required>
                                                @foreach ($countries as $key => $country)
                                                    <option data-mobile_code="{{ $country->dial_code }}"
                                                        value="{{ $country->country }}" data-code="{{ $key }}">
                                                        {{ __($country->country) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="mobile" class="form--label"> @lang('Mobile')</label>
                                            <div class="input-group">
                                                <span class="input-group-text mobile-code bg--base">

                                                </span>
                                                <input type="hidden" name="mobile_code">
                                                <input type="hidden" name="country_code">
                                                <input type="number" name="mobile" value="{{ old('mobile') }}"
                                                    class="form-control form--control checkUser" id="mobile" required>
                                            </div>
                                            <small class="text-danger mobileExist"></small>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="password" class="form-label">@lang('Password')</label>
                                            <div class="input-group">
                                                <input type="password" class="form--control" name="password" id="password"
                                                    required>
                                                <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                                    id="password"></div>
                                            </div>
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

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <label class="form-label">@lang('Confirm Password')</label>
                                                <div class="input-group">
                                                    <input type="password" class="form--control"
                                                        name="password_confirmation" required>
                                                    <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                                        id="password_confirmation"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="captcha-img">
                                            <x-captcha />
                                        </div>
                                    </div>

                                    @if ($general->agree)
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="form--check">
                                                    <input type="checkbox" class="form-check-input" id="agree"
                                                        @checked(old('agree')) name="agree" required>
                                                    <div class="form-check-label">
                                                        @lang('I agree with')
                                                        <label class="form-label" for="agree">
                                                            @foreach ($policyPages as $policy)
                                                                <a href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}"
                                                                    class="text--base">{{ __($policy->data_values->title) }}</a>
                                                                @if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn--base w-100">@lang('Register')</button>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="have-account text-center">
                                            <p class="have-account__text">@lang('Already Have An Account')?
                                                <a href="{{ route('user.login') }}"
                                                    class="have-account__link text--base">
                                                    @lang('Login')
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-6">
                        <div class="account-thumb">
                            <img src="{{ getImage('assets/images/frontend/register/' . @$registerContent->data_values->image, '550x480') }}"
                                alt="Register Image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal custom--modal fade" id="existModalCenter" tabindex="-1" role="dialog"
        aria-labelledby="existModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content ">
                <div class="modal-header ">
                    <h5 class="modal-title " id="existModalLongTitle">@lang('You are with us')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body  ">
                    <h6 class="text-center ">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn--secondary btn-sm text-black"
                        data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.login') }}" class="btn btn--base btn-sm">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection

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
            @if ($general->secure_password)
                $('input[name=password]').on('input', function() {
                    secure_password($(this));
                });
                $('[name=password]').focus(function() {
                    $(this).closest('.form-group').addClass('hover-input-popup');
                });
                $('[name=password]').focusout(function() {
                    $(this).closest('.form-group').removeClass('hover-input-popup');
                });
            @endif
            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
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
