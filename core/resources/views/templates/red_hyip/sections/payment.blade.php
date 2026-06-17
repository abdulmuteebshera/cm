@php
    $paymentElement = getContent('payment.element', orderById: true);
@endphp
<div class="payment-section py-60">
    <div class="container">
        <div class="row gy-4 justify-content-center">
            @foreach ($paymentElement as $payment)
            <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                <div class="payment-item">
                    <img src="{{getImage('assets/images/frontend/payment/' . @$payment->data_values->image, '105x35')}}" alt="">
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

