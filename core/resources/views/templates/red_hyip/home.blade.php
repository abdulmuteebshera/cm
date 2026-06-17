@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $bannerContent = getContent('banner.content', true);
    @endphp
    <!--========================== Banner Section Start ==========================-->
    <section class="banner-section">
        <div class="banner bg-img"
            style="background-image: url({{ asset($activeTemplateTrue . 'images/shapes/banner-bg-black.png') }});">
            <div class="banner__mask"></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="banner-content">
                            <h1 class="banner-content__title">{{ __(@$bannerContent->data_values->heading) }}</h1>
                            <p class="banner-content__desc">{{ __(@$bannerContent->data_values->sub_heading) }}</p>
                            <div class="banner-content__button">
                                <a href="{{ @$bannerContent->data_values->button_link }}"
                                    class="btn btn--base">{{ __(@$bannerContent->data_values->button_name) }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="banner-thumb">
                            <img src="{{ getImage('assets/images/frontend/banner/' . @$bannerContent->data_values->image,'900x600') }}"
                                alt="@lang('Banner image')">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--========================== Banner Section End ==========================-->
    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
