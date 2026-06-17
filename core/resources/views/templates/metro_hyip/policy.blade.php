@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="policy-section pt-120">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="policy-right__wrapper">
                    @php
                        echo $policy->data_values->details;
                    @endphp
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
