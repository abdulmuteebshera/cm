@php
    $about = getContent('about.content', true);
@endphp
<div class="about-section pt-120">
    <div class="container">
        <div class="row gy-5 flex-wrap-reverse">
            <div class="col-lg-6 pe-lg-5">
                <div class="about-thumb">
                    <div class="about-thumb__inner">
                        <img src="{{ __(getImage('assets/images/frontend/about/'.@$about->data_values->image,'600x580')) }}" alt="image">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content">
                    <div class="section-heading mb-2">
                        <span class="section-heading__subtitle"> {{ __(@$about->data_values->sub_heading) }} </span>
                        <h2 class="section-heading__title"> {{ __(@$about->data_values->heading) }} </h2>
                    </div>

                    <p class="about-content__desc">
                      {{ __(@$about->data_values->content) }}
                    </p>
                    <ul class="about-content__list">
                        <li class="about-content__item"> <span class="about-content__icon"><i
                                    class="las la-check"></i></span> {{ __(@$about->data_values->point_one) }}</li>
                        <li class="about-content__item"> <span class="about-content__icon"><i
                                    class="las la-check"></i></span> {{ __(@$about->data_values->point_two) }}</li>
                        <li class="about-content__item"> <span class="about-content__icon"><i
                                    class="las la-check"></i></span> {{ __(@$about->data_values->point_three) }}</li>
                        <li class="about-content__item"> <span class="about-content__icon"><i
                                    class="las la-check"></i></span> {{ __(@$about->data_values->point_three) }}</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
