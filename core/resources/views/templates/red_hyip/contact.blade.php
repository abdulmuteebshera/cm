@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $contactContent = getContent('contact.content', true);
        $contactElement = getContent('contact.element', orderById: true);
    @endphp
    <section class="contact-top pt-120 pb-60">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                @foreach ($contactElement as $contact)
                    <div class="col-lg-4 col-md-6">
                        <div class="contact-item">
                            <div class="contact-item__shape"></div>
                            <div class="contact-item__box">
                                <div class="contact-item__icon">
                                    @php echo $contact->data_values->icon; @endphp
                                </div>
                                <div class="contact-item__content">
                                    <h4 class="contact-item__title">{{ __(@$contact->data_values->title) }}</h4>
                                    <p class="contact-item__desc">
                                        {{ __(@$contact->data_values->content) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="contact-bottom pt-60 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="contactus-form">
                        <h3 class="contact__title text-center"> @lang('Get In Touch With Us')</h3>
                        <form autocomplete="off" method="post" action="" class="verify-gcaptcha">
                            @csrf
                            <div class="row gy-md-4 gy-3">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Name')</label>
                                        <input name="name" type="text" class="form--control"
                                                    value="@if (auth()->user()) {{ auth()->user()->fullname }}@else{{ old('name') }}@endif"
                                                    @if (auth()->user()) readonly @endif required>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Email')</label>
                                        <input name="email" type="email" class="form--control"
                                            value="@if (auth()->user()) {{ auth()->user()->email }}@else{{ old('email') }} @endif"
                                            @if (auth()->user()) readonly @endif required>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Subject')</label>
                                        <input name="subject" type="text" class="form--control"
                                            value="{{ old('subject') }}" required>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Message')</label>
                                        <textarea name="message" wrap="off" class="form--control" required>{{ old('message') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <x-captcha />
                                </div>

                                <div class="col-sm-12">
                                    <button class=" btn btn--base w-100"> @lang('Send Your Message') <span
                                            class="button__icon ms-1"><i class="fas fa-paper-plane"></i></span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact">
        <div class="contact-map">
            <iframe src="{{ @$contactContent->data_values->map_url }}" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

@endsection
