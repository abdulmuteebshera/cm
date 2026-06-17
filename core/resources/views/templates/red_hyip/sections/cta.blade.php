@php
    $ctaContent = getContent('cta.content', true);
@endphp
<section class="referral-section py-60">
    <div class="referral-section__tree">
        <img src="{{ getImage('assets/images/frontend/cta/' . @$ctaContent->data_values->image_right,'355x540') }}" alt="">
    </div>
    <div class="container">
        <div class="row gy-5 align-items-center flex-wrap-reverse">
            <div class="col-lg-5">
                <div class="referral-thumb">
                    <img src="{{ getImage('assets/images/frontend/cta/' . @$ctaContent->data_values->image_left,'525x435') }}" alt="">
                    <div class="referral-thumb__mask"> </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="referral-content">
                    <div class="section-heading style-two mb-0">
                        <h2 class="section-heading__title">{{ __($ctaContent->data_values->heading) }}</h2>
                        <p class="section-heading__desc">{{ __($ctaContent->data_values->sub_heading) }}</p>
                        <div class="referral-content__button">
                            <a href="{{ $ctaContent->data_values->button_link }}"
                                class="btn btn--base">{{ __($ctaContent->data_values->button_name) }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



