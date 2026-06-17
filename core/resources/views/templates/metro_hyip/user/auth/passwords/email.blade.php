@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="account">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5 account-wrapper">
                    <div class="account-wrapper-inner">
                        <div class="account-form">
                            <div class="mb-4">
                                <p>@lang('To recover your account please provide your email or username to find your account.')</p>
                            </div>
                            <form method="POST" action="{{ route('user.password.email') }}">
                                @csrf
                                <div class="form-group">
                                    <label>@lang('Email or Username')</label>
                                    <input type="text" class="form-control form--control" name="value"
                                        value="{{ old('value') }}" required autofocus="off">
                                </div>
                                <x-captcha />
                                <div class="form-group">
                                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
         $('[name="captcha"]').attr("placeholder", "Enter captcha").prev('.form-label').hide();
    </script>
@endpush
