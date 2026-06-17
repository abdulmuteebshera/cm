@php
    $socialElement  = getContent('social_icon.element', orderById: true);
    $policyPages    = getContent('policy_pages.element', orderById: true);
    $pages          = App\Models\Page::where('tempname', $activeTemplate)->where('is_default', 0)->latest('id')->get();
    $contactElement = getContent('contact.element', orderById: true);
    $footerContent  = getContent('footer.content', true);
@endphp
<footer class="footer-section-wrapper">
    <div class="footer-section bg-img"
        style="background-image: url({{ asset($activeTemplateTrue . 'images/shapes/footer-img.png') }});">
        <div class="footer-section__shape"></div>
        <div class="footer-section__img">
            <img src="{{ getImage('assets/images/frontend/footer/' . @$footerContent->data_values->image,'300x275') }}" alt="@lang('Footer image')">
        </div>
        <div class="pb-60 pt-120">
            <div class="container">
                <div class="row justify-content-center gy-5">
                    <div class="col-xl-3 col-sm-6">
                        <div class="footer-item">
                            <h5 class="footer-item__title">@lang('About Us')</h5>
                            <p class="footer-item__desc">{{ __(@$footerContent->data_values->content) }}</p>

                            <ul class="social-list">
                                @foreach ($socialElement as $social)
                                    <li class="social-list__item"><a href="{{ @$social->data_values->url }}"
                                            class="social-list__link">@php echo $social->data_values->icon; @endphp</a> </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-1 d-xl-block d-none"></div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="footer-item">
                            <h5 class="footer-item__title">@lang('Site Urls')</h5>
                            <ul class="footer-menu">
                                <li class="footer-menu__item"><a href="{{ route('blogs') }}" class="footer-menu__link">@lang('Blogs')</a></li>
                                <li class="footer-menu__item"><a href="{{ route('contact') }}" class="footer-menu__link">@lang('Contact Us')</a></li>
                                <li class="footer-menu__item"><a href="{{ route('plan') }}"
                                        class="footer-menu__link">@lang('Plans')</a></li>
                                @foreach ($pages as $data)
                                    <li class="footer-menu__item">
                                        <a href="{{ route('pages', [$data->slug]) }}" class="footer-menu__link">{{ __($data->name) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="footer-item">
                            <h5 class="footer-item__title">@lang('Useful Link')</h5>
                            <ul class="footer-menu">
                                @foreach ($policyPages as $policy)
                                    <li class="footer-menu__item">
                                        <a href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}"
                                            class="footer-menu__link">{{ __($policy->data_values->title) }}</a>
                                    </li>
                                @endforeach
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
                                        @php echo @$contact->data_values->icon;
                                    @endphp
                                    </div>
                                    <div class="footer-contact-menu__item-content">
                                        {{ @$contact->data_values->content }}
                                    </div>
                                </li>
                            @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom-footer section-bg py-3">
            <div class="container">
                <div class="row gy-3">
                    <div class="col-md-12 text-center">
                        <p> @lang('Copyright') &copy; <?php echo date('Y'); ?> @lang('All Right Reserved.') </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
