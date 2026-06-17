@php
    $trx = \App\Models\Transaction::with('user')
        ->latest()
        ->limit(10)
        ->get();
        
    $fakeTransaction = \App\Models\Frontend::where('data_keys', 'transaction.element')
        ->limit(10)
        ->get();
    
    $transaction        = $trx->merge($fakeTransaction);
    $transactions       = $transaction->sortByDesc('created_at')->take(10);
    $transactionContent = getContent('transaction.content', true);
    
@endphp

<div class="pt-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-heading style-two">
                    <span class="section-heading__subtitle">{{ __(@$transactionContent->data_values->sub_heading) }}
                    </span>
                    <h1 class="section-heading__title"> {{ __(@$transactionContent->data_values->heading) }} </h1>
                </div>

                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                          @foreach ($transactions as $data)
                            @if (@$data->data_values)
                            <td>{{ __(@$data->data_values->name) }}</td>
                            <td>{{ @$data->data_values->date }}</td>
                            <td>{{ @$data->data_values->amount }} {{ $general->cur_text }}</td>
                            <td>{{ __(@$data->data_values->gateway) }}</td>
                            @else
                            <td>{{ __($data->user->fullname) }}</td>
                            <td>{{ showDateTime($data->created_at, 'Y-m-d') }}</td>
                            <td>{{ getAmount($data->amount) }} {{ $general->cur_text }}</td>
                            <td>{{ __($data->details) }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
