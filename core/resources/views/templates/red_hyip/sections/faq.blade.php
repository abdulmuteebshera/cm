@php
    $faqContent = getContent('faq.content', true);
    $faqElement = getContent('faq.element', orderById: true);
@endphp

<section class="faq-section pt-120 mb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-heading">
                    <h2 class="section-heading__title">{{ __(@$faqContent->data_values->heading) }}</h2>
                </div>
            </div>
        </div>
        <div class="row gy-md-5 gy-4 justify-content-center">
            <div class="col-lg-6">
                <ul class="faq-list">
                    @foreach ($faqElement as $faq)
                        @if ($loop->odd)
                            <li class="faq-list__item">
                                <div class="faq-list__icon">?</div>
                                <div class="faq-list__content">
                                    <h4 class="faq-list__title">{{ __($faq->data_values->question) }}</h4>
                                    <p class="faq-list__desc">{{ __($faq->data_values->answer) }}</p>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-6">
                <ul class="faq-list">
                    @foreach ($faqElement as $faq)
                        @if ($loop->even)
                            <li class="faq-list__item">
                                <div class="faq-list__icon">?</div>
                                <div class="faq-list__content">
                                    <h4 class="faq-list__title">{{ __($faq->data_values->question) }}</h4>
                                    <p class="faq-list__desc">{{ __($faq->data_values->answer) }}</p>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
