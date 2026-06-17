@php
    $testimonialContent = getContent('testimonial.content', true);
    $testimonialElement = getContent('testimonial.element', orderById: true);
@endphp
<section class="testimonails-section py-60">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                <div class="testimonials-item-wrapper bg-img" style="background-image: url({{ getImage('assets/images/frontend/testimonial/' . @$testimonialContent->data_values->image, '690x300') }}">

                    @foreach ($testimonialElement as $testimonial)
                    <div class="testimonials-item">
                        <div class="testimonials-item__thumb">
                            <div class="testimonials-item__mask"></div>
                            <img src="{{ getImage('assets/images/frontend/testimonial/' . @$testimonial->data_values->image, '100x100') }}" alt="">
                        </div>
                        <div class="testimonials-item__content">
                            <h4 class="testimonials-item__name">{{ __(@$testimonial->data_values->author) }}</h4>
                            <span class="testimonials-item__designation">{{ __(@$testimonial->data_values->designation) }}</span>
                            <p class="testimonials-item__desc">{{ __(@$testimonial->data_values->quote) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

