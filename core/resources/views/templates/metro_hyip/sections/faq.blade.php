@php
    $faq = getContent('faq.content', true);
    $faqElement = getContent('faq.element', false, null, true);
@endphp
<section class="faq-section pt-120 pb-120">
    <div class="faq-section__shape-one">
        <img src="{{ asset($activeTemplateTrue . 'images/shapes/planet-small.png') }}" alt="">
    </div>
    <div class="faq-section__shape-two">
        <img src="{{ asset($activeTemplateTrue . 'images/shapes/planet-bg.png') }}" alt="">
    </div>
    <div class="container">
        <div class="row align-items-center gy-5 flex-wrap-reverse">
            <div class="col-lg-5 col-md-5 pe-lg-5">
                <div class="faq-thumb">
                    <img src="{{ getImage('assets/images/frontend/faq/'.@$faq->data_values->image,'490x440') }}" alt="">
                </div>
            </div>
            <div class="col-lg-7 col-md-7">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-heading">
                            <span class="section-heading__subtitle"> {{ __(@$faq->data_values->sub_heading)}} </span>
                            <h2 class="section-heading__title"> {{ __(@$faq->data_values->heading)}} </h2>
                        </div>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="accordion custom--accordion" id="accordionExample">
                      @foreach ($faqElement as $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $loop->index }}" aria-expanded="{{$loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $loop->index }}">
                                    {{ __(@$item->data_values->question) }}
                                </button>
                            </h2>
                            <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse @if($loop->first) show @endif" aria-labelledby="heading{{ $loop->index }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <p class="text">
                                      {{ __(@$item->data_values->answer) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


