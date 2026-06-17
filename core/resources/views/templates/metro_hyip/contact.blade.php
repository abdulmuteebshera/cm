@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $contactContent = getContent('contact.content', true);
        $contactElement = getContent('contact.element', null, 3, true);
        $user = auth()->user();
    @endphp

    <!-- ==================== Contact Form & Map Start Here ==================== -->
    <section class="contact-top pt-120">
        <div class="container">
            <div class="contact__bg">
                <div class="row gy-lg-3 gy-4 flex-wrap">
                    <div class="col-lg-6">
                        <div class="contactus-form">
                            <h2 class="contact__title"> {{ __(@$contactContent->data_values->heading) }} </h2>
                            <p class="contact__desc"> {{ __(@$contactContent->data_values->sub_heading) }} </p>
                            <form action="{{ route('contact') }}" method="POST" autocomplete="off" class="verify-gcaptcha">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="name" class="form--control"
                                        placeholder="@lang('Fullname')" value="{{ old('name', @$user->fullname) }}"
                                        @if ($user) readonly @endif required>
                                </div>
                                <div class="form-group">
                                    <input type="email" name="email" class="form--control"
                                        placeholder="@lang('E-mail')" value="{{ old('email', @$user->email) }}"
                                        @if ($user) readonly @endif required>
                                </div>
                                <div class="form-group">
                                    <input type="email" name="subject" class="form--control"
                                        placeholder="@lang('Enter Subject')" value="{{ old('subject') }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <textarea class="form--control" name="message" placeholder="@lang('Write Your Message')" required>{{ old('message') }}</textarea>
                                </div>

                                <x-captcha />

                                <div class="form-group">
                                    <button class="btn btn--base w-100"> @lang('Send Message') </button>
                                </div>

                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 pe-lg-5">
                        <div class="contact-map">
                            <iframe src=" {{ $contactContent->data_values->map }}" allowfullscreen=""  loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ==================== Contact Form & Map End Here ==================== -->
    <!-- ==================== Contact Start Here ==================== -->
    <section class="contact-bottom pt-120 pb-60">
        <div class="container">
            <div class="row gy-4">
                @foreach ($contactElement as $contact)
                    <div class="col-lg-4 col-md-6">
                        <div class="contact-item">
                            <div class="contact-item__icon"> @php echo $contact->data_values->icon @endphp</div>
                            <div class="contact-item__content">
                                <h4 class="contact-item__title"> {{ __(@$contact->data_values->title) }} </h4>
                                <p class="contact-item__desc">{{ __(@$contact->data_values->content) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('[name="captcha"]').attr("placeholder", "Enter captcha").prev('.form-label').hide();
        })(jQuery);
    </script>
@endpush
