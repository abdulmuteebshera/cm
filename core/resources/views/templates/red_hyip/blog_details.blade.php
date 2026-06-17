@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-detials pt-120 pb-60">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                <div class="col-lg-8 pe-xl-5">
                    <div class="blog-details-item">
                        <div class="blog-details-item__thumb">
                            <img src="{{ getImage('assets/images/frontend/blog/' . @$blog->data_values->image, '800x640') }}"
                                alt="">
                            <div class="blog-item__date">{{ showDateTime($blog->created_at, 'd') }} <span
                                    class="text">{{ showDateTime($blog->created_at, 'M') }}</span></div>
                        </div>
                        <div class="blog-details-item__content">
                            <h3 class="blog-details-item__title"> {{ __($blog->data_values->title) }} </h3>

                            <p class="blog-details-item__desc">
                                @php
                                    echo $blog->data_values->description;
                                @endphp
                            </p>

                            <!-- Tags And Social Share -->
                            <div class="share-tag-wrapper">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-5 col-sm-12">
                                        <ul class="social-list style-two justify-content-md-end">
                                            <li class="social-list__item"><span
                                                    class="social-list__share text--base me-2 fs-4"><i
                                                        class="fas fa-share-alt"></i></span> </li>
                                            <li class="social-list__item"><a
                                                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                                    class="social-list__link"><i class="fab fa-facebook-f"></i></a>
                                            </li>
                                            <li class="social-list__item"><a
                                                    href="https://twitter.com/intent/tweet?text={{ __($blog->data_values->title) }}&amp;url={{ urlencode(url()->current()) }}"
                                                    class="social-list__link"> <i class="fab fa-twitter"></i></a></li>
                                            <li class="social-list__item"><a
                                                    href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __($blog->data_values->title) }}&amp;summary=dit is de linkedin summary"
                                                    class="social-list__link"> <i class="fab fa-linkedin-in"></i></a></li>
                                            <li class="social-list__item"><a
                                                    href="https://api.whatsapp.com/send?text={{ urlencode(url()->current()) }}"
                                                    class="social-list__link"> <i class="lab la-whatsapp"></i></a></li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog-sidebar-wrapper">
                        <div class="row gy-5">
                            <div class="col-lg-12">
                                <div class="blog-sidebar">
                                    <h5 class="blog-sidebar__title"> @lang('Popular News') </h5>
                                    @foreach ($blogs as $blog)
                                        <div class="latest-blog">
                                            <div class="latest-blog__thumb">
                                                <a href="{{ route('blog.details', [slug($blog->data_values->title), $blog->id]) }}"> <img
                                                        src="{{ getImage('assets/images/frontend/blog/thumb_' . @$blog->data_values->image, '400x320') }}"
                                                        alt=""></a>
                                            </div>
                                            <div class="latest-blog__content">
                                                <h6 class="latest-blog__title"><a
                                                        href="{{ route('blog.details', [slug($blog->data_values->title), $blog->id]) }}">{{ __($blog->data_values->title) }}</a>
                                                </h6>
                                                <span
                                                    class="latest-blog__date">{{ showDateTime($blog->created_at, 'Y M d') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush
