@php
    $howWork = getContent('how_work.content', true);
    $howWorElement = getContent('how_work.element', null, false, true);
@endphp
<section class="process-section pt-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-heading style-two">
                    <span class="section-heading__subtitle"> {{ __(@$howWork->data_values->sub_heading) }} </span>
                    <h2 class="section-heading__title"> {{ __(@$howWork->data_values->heading) }} </h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="process justify-content-center">
                    @foreach ($howWorElement as $key => $value)
                        <div class="process-item">
                            <div class="process-item__thumb">
                                <img src="{{ getImage('assets/images/frontend/how_work/' . @$value->data_values->image, '115x100') }}">
                            </div>
                            <h5 class="process-item__title">
                                {{ $key + 1 }}.{{ __($value->data_values->title) }}
                            </h5>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
