@php
    $choosUsContent = getContent('why_choose.content', true);
    $choseUsElement = getContent('why_choose.element', orderById: true);
@endphp
<section class="join-us-section pt-120 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-heading">
                    <h2 class="section-heading__title">{{ __(@$choosUsContent->data_values->heading) }}</h2>
                    <p class="section-heading__desc">{{ __(@$choosUsContent->data_values->sub_heading) }}</p>
                </div>
            </div>
        </div>
        <div class="row gy-4 align-items-center">
            <div class="col-lg-4">
                <div class="row join-us-wrapper">
                    @foreach ($choseUsElement as $choose)
                        @if ($loop->odd)
                            <div class="col-lg-12 col-md-6">
                                <div class="join-card">
                                    <div class="join-card__icon">
                                        @php  echo $choose->data_values->icon;  @endphp
                                    </div>
                                    <div class="join-card__content">
                                        <h5 class="join-card__title">{{ __($choose->data_values->title) }}</h5>
                                        <p class="join-card__desc"> {{ __($choose->data_values->content) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4">
                <div class="join-thumb">
                    <div class="join-thumb__shape"></div>
                    <div class="join-thumb__border"></div>
                    <img src="{{ getImage('assets/images/frontend/why_choose/' . @$choosUsContent->data_values->image, '800x700') }}"
                        alt="" class="join-thumb__img">
                    <div class="join-thumb__dollars">
                        <img src="{{ asset($activeTemplateTrue . 'images/thumbs/join-dollar.png') }}" alt=""
                            class="join-thumb__dollar one">
                        <img src="{{ asset($activeTemplateTrue . 'images/thumbs/join-dollar.png') }}" alt=""
                            class="join-thumb__dollar two">
                        <img src="{{ asset($activeTemplateTrue . 'images/thumbs/join-dollar.png') }}" alt=""
                            class="join-thumb__dollar three">
                        <img src="{{ asset($activeTemplateTrue . 'images/thumbs/join-dollar.png') }}" alt=""
                            class="join-thumb__dollar four">
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="row join-us-wrapper">
                    @foreach ($choseUsElement as $choose)
                        @if ($loop->even)
                            <div class="col-lg-12 col-md-6">
                                <div class="join-card">
                                    <div class="join-card__icon">
                                        @php  echo $choose->data_values->icon;  @endphp
                                    </div>
                                    <div class="join-card__content">
                                        <h5 class="join-card__title">{{ __($choose->data_values->title) }}</h5>
                                        <p class="join-card__desc"> {{ __($choose->data_values->content) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
