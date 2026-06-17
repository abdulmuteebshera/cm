@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="account">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="my-4">
                        <h3>@lang('Complete Your Profile')</h3>
                        <p>@lang('You must complete your profile by providing the required information').</p>
                    </div>
                    <div class="account-wrapper">
                        <div class="account-wrapper-inner">
                            <div class="account-form">
                                <form method="POST" action="{{ route('user.data.submit') }}">
                                    @csrf
                                    <div class="row justify-content-center align-items-center">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form--label">@lang('First Name')</label>
                                                <input type="text" class="form--control" name="firstname"
                                                    value="{{ old('firstname') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form--label">@lang('Last Name')</label>
                                                <input type="text" class="form--control" name="lastname"
                                                    value="{{ old('lastname') }}" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="form--label">@lang('Address')</label>
                                            <input type="text" class="form--control" name="address"
                                                value="{{ old('address') }}">
                                        </div>

                                        <div class="form-group">
                                            <label class="form--label">@lang('State')</label>
                                            <input type="text" class="form--control" name="state"
                                                value="{{ old('state') }}">
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form--label">@lang('Zip Code')</label>
                                                <input type="text" class="form--control" name="zip"
                                                    value="{{ old('zip') }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group ">
                                                <label class="form--label">@lang('City')</label>
                                                <input type="text" class="form--control" name="city"
                                                    value="{{ old('city') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100">
                                            @lang('Submit')
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
