@extends($activeTemplate.'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <h3 class="mb-2">@lang('Withdraw Confirmation')</h3>
                <p class="mb-1">@lang('Provide the below information and re-check the information before submitting. The withdrawal amount will be sent to the given information.')</p>
                <p class="mb-1">@lang('So you\'ve to ensure that the information is correct. Otherwise, the authority will not be responsible for any economic loss.')</p>
                <p>@lang('The processing may take some time. Once the amount sends, the system\'s admin will approve the request.')</p>
            </div>
            <div class="card custom--card mb-4">
                <div class="card-body">
                    <ul class="list-group text-center">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Amount')</span>
                            <span class="fw-bold">{{ showAmount($withdraw->amount) }} {{ __($general->cur_text) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Charge')</span>
                            <span class="fw-bold">{{ showAmount($withdraw->charge) }} {{ __($general->cur_text) }}</span>
                        </li>
                        @if($withdraw->management_fee > 0)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Management Fee')</span>
                            <span class="fw-bold">{{ showAmount($withdraw->management_fee) }} {{ __($general->cur_text) }}</span>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Receivable')</span>
                            <span class="fw-bold">{{ showAmount($withdraw->after_charge) }} {{ __($general->cur_text) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('In') {{ $withdraw->currency }}</span>
                            <span class="fw-bold">{{ showAmount($withdraw->final_amount) }} {{ $withdraw->currency }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card custom--card">
                <div class="card-body">
                    <form action="{{route('user.withdraw.submit')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            @php
                                echo $withdraw->method->description;
                            @endphp
                        </div>
                        <x-viser-form identifier="id" identifierValue="{{ $withdraw->method->form_id }}" />
                        @if(auth()->user()->ts)
                        <div class="form-group">
                            <label>@lang('Google Authenticator Code')</label>
                            <input type="text" name="authenticator_code" class="form-control form--control" required>
                        </div>
                        @endif
                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
