@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="account">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7 col-xl-5 account-wrapper">
                <div class="account-wrapper-inner">
                    <div class="account-form">
                        <div class="mb-4">
                            <p>@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>
                        </div>
                        <form method="POST" action="{{ route('user.password.update') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group">
                                <label for="password" class="form--label required">@lang('Password')</label>
                                <div class="input-group">
                                    <input id="your-password" type="password" class="form--control" name="password"
                                        required>
                                    <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#password">
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

                            <div class="form-group">
                                <label for="password_confirmation" class="form--label">@lang('Confirm Password')</label>
                                <div class="input-group">
                                    <input id="password_confirmation" type="password" class="form--control"
                                        name="password_confirmation" required>
                                    <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                        id="#password_confirmation"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn--base w-100"> @lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@if ($general->secure_password)
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
@push('script')
    <script>
        (function($) {
            "use strict";
            $('input[name=password]').on('input', function() {
                secure_password($(this));
            });

            $('[name=password]').focus(function() {
                $(this).closest('.form-group').addClass('hover-input-popup');
            });

            $('[name=password]').focusout(function() {
                $(this).closest('.form-group').removeClass('hover-input-popup');
            });
        })(jQuery);
    </script>
@endpush
