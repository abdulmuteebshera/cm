@php
    $links = getContent('policy_pages.element', false, null, true);
    $footer = getContent('footer.content', true);
    $contactElement = getContent('contact.element', orderById: true);
@endphp
<footer class="footer-area pt-60">
    <div class="footer-area-inner bg-img"
        style="background-image: url({{ asset($activeTemplateTrue . 'images/shapes/footer-05.png') }});">
        <div class="footer-area__bg">
        </div>
        <div class="footer-shape">
            <img src="{{ getImage('assets/images/frontend/footer/' . $footer->data_values->image, '240x580') }}"
                alt="">
        </div>
        <div class="pb-60 pt-120">
            <div class="container">
                <div class="row justify-content-center gy-5">
                    <div class="col-xl-3 col-sm-6">
                        <div class="footer-item">
                            <div class="footer-item__logo">
                                <a href="{{ route('home') }}"> <img
                                        src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt=""></a>
                            </div>
                            <p class="footer-item__desc"> {{ __($footer->data_values->content) }}</p>
                        </div>
                    </div>
                    <div class="col-xl-1 d-xl-block d-none"></div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="footer-item">
                            <h5 class="footer-item__title">@lang('Site Link')</h5>
                            <ul class="footer-menu">
                                <li class="footer-menu__item"><a href="{{ route('home') }}"
                                        class="footer-menu__link">@lang('Home')</a>
                                </li>
                                @foreach ($links as $link)
                                    <li class="footer-menu__item"><a href="{{ route('policy.pages', [slug($link->data_values->title), $link->id]) }}" class="footer-menu__link">{{ __($link->data_values->title) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="footer-item">
                            <h5 class="footer-item__title"> @lang('Important Link') </h5>
                            <ul class="footer-menu">
                                <li class="footer-menu__item"><a href="{{ route('plan')}}" class="footer-menu__link">  @lang('Plan')</a></li>
                                <li class="footer-menu__item"><a href="{{ route('blogs')}}" class="footer-menu__link">@lang('Blog')
                                    </a></li>
                                <li class="footer-menu__item"><a href="{{ route('contact')}}" class="footer-menu__link">@lang('Contact Us') </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-1 d-xl-block d-none"></div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="footer-item">
                            <h5 class="footer-item__title">@lang('Contact Info')</h5>
                            <ul class="footer-contact-menu">
                                @foreach ($contactElement as $contact)
                                <li class="footer-contact-menu__item">
                                    <div class="footer-contact-menu__item-icon">
                                        @php echo @$contact->data_values->icon; @endphp
                                    </div>
                                    <div class="footer-contact-menu__item-content">
                                        <p>{{ __(@$contact->data_values->content) }} </p>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Top End-->

        <!-- bottom Footer -->
        <div class="bottom-footer py-3">
            <div class="container">
                <div class="row gy-3">
                    <div class="col-md-12 text-center">
                        <div class="bottom-footer-text"> &copy; {{ date('Y') }} <a
                                href="{{ route('home') }}">{{ __($general->site_name) }}</a> @lang('All Rights Reserved').</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
