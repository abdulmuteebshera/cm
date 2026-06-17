@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-detials pt-120">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                <div class="col-xl-9 col-lg-8">
                    <div class="blog-details">
                        <div class="blog-details__thumb">
                            <img src="{{ getImage('assets/images/frontend/blog/' . @$blog->data_values->image, '965x450') }}"
                                alt="">
                        </div>
                        <div class="blog-details__content">
                            <ul class="content-list">
                                <li class="content-list__item"> {{ showDateTime($blog->created_at, 'F j, Y') }} </li>
                                <li class="content-list__item "> {{ diffForHumans($blog->created_at) }} </li>
                            </ul>
                            <h3 class="blog-details__title">{{ __(@$blog->data_values->title) }}</h3>
                            <p class="blog-details__desc">
                            <p> @php  echo $blog->data_values->description;  @endphp</p>
                        </div>
                        <div class="tag">
                            <div class="blog-details__share d-flex align-items-center flex-wrap mt-2">
                                <h5 class="social-share__title mb-0 me-sm-3 me-1 d-inline-block">@lang('Share') </h5>
                                <ul class="social-list">
                                    <li class="social-list__item"><a
                                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                            class="social-list__link active"><i class="fab fa-facebook-f"></i></a> </li>
                                    <li class="social-list__item"><a
                                            href="https://twitter.com/intent/tweet?text={{ __($blog->data_values->title) }}&amp;url={{ urlencode(url()->current()) }}"
                                            class="social-list__link"> <i class="fab fa-twitter"></i></a></li>
                                    <li class="social-list__item"><a
                                            href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __($blog->data_values->title) }}&amp;summary=dit is de linkedin summary"
                                            class="social-list__link"> <i class="fab fa-linkedin-in"></i></a></li>
                                    <li class="social-list__item"><a
                                            href="https://api.whatsapp.com/send?text={{ urlencode(url()->current()) }}"
                                            class="social-list__link"> <i class="fab fa-instagram"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-xl-3 col-lg-4">
                    <!-- ============================= Blog Details Sidebar Start ======================== -->
                    <div class="blog-sidebar-wrapper">
                        <div class="blog-sidebar">
                            <h5 class="blog-sidebar__title"> @lang('Popular News') </h5>
                            <ul class="text-list style-category">
                                @foreach ($blogs as $blog)
                                    <li class="text-list__item">
                                        <a class="blog-sidebar__thumb"
                                            href="{{ route('blog.details', [slug($blog->data_values->title), $blog->id]) }}">
                                            <img class="thumb_link"
                                                src="{{ getImage('assets/images/frontend/blog/thumb_' . @$blog->data_values->image, '415x305') }}"
                                                alt="">
                                            <div class="blog-sidebar__content">
                                                <p>{{ __($blog->data_values->title) }} </p>
                                                <div class="blog-created">
                                                    <span><i class="las la-calendar-week"></i></span>
                                                    <small>{{ showDateTime($blog->created_at, 'F j, Y') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <!-- ============================= Blog Details Sidebar End ======================== -->
                </div>
            </div>
        </div>
    </section>
@endsection

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush
