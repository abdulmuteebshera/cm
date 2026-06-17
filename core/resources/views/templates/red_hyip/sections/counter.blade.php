@php
    $counterElement = getContent('counter.element', orderById: true);
@endphp
<section class="counter-up py-60">
    <div class="container">
        <div class="row gy-4">
            @foreach ($counterElement as $counter)
            <div class="col-lg-3 col-sm-6 col-6">
                <div class="counterup-item">
                    <div class="counterup-item__number">
                        <h2 class="counterup-item__title mb-4"><span>{{$counter->data_values->counter_digit }}</span></h2>
                   </div>
                   <span class="counterup-item__text">{{ __($counter->data_values->title) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
