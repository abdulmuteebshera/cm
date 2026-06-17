 @php
     $latestDeposit = \App\Models\Deposit::with('user', 'gateway')
         ->where('status', 1)
         ->latest()
         ->limit(10)
         ->get();
     $fakeDeposit = \App\Models\Frontend::where('data_keys', 'transaction.element')
         ->whereJsonContains('data_values->trx_type', 'deposit')
         ->limit(10)
         ->get();
     $deposits = $latestDeposit->merge($fakeDeposit);
     $deposits = $deposits->sortByDesc('created_at')->take(10);

     $latestWithdraw = \App\Models\Withdrawal::with('user', 'method')
         ->where('status', 1)
         ->latest()
         ->limit(10)
         ->get();
     $fakeWithdraw = \App\Models\Frontend::where('data_keys', 'transaction.element')
         ->whereJsonContains('data_values->trx_type', 'withdraw')
         ->limit(10)
         ->get();

     $withdrawals = $latestWithdraw->merge($fakeWithdraw);
     $withdrawals = $withdrawals->sortByDesc('created_at')->take(10);
     $transactionContent = getContent('transaction.content', true);
 @endphp

 <section class="transaction py-60">
     <div class="container">
         <div class="row">
             <div class="col-lg-12">
                 <div class="section-heading">
                     <h2 class="section-heading__title">{{ __($transactionContent->data_values->heading) }} </h2>
                     <p class="section-heading__desc">{{ __($transactionContent->data_values->sub_heading) }}</p>
                 </div>
             </div>
         </div>
         <div class="row gy-5">
             <div class="col-lg-6">
                 <h3 class="table-heading">@lang('Latest Deposits')</h3>
                 <table class="table table--responsive--lg">
                     <thead>
                         <tr>
                             <th>@lang('Gatway')</th>
                             <th>@lang('Date')</th>
                             <th>@lang('Amount')</th>
                         </tr>
                     </thead>
                     <tbody>
                         @foreach ($deposits as $deposit)
                             <tr>
                                 @if (@$deposit->data_values)
                                     <td data-label="@lang('Gatway')">
                                         {{ __(@$deposit->data_values->gateway) }}
                                     </td>
                                     <td data-label="@lang('date')">
                                         {{ showDateTime(@$deposit->data_values->created_at) }}
                                     </td>
                                     <td data-label="@lang('Amount')">
                                         {{ showAmount(@$deposit->data_values->amount) }}
                                         {{ $general->cur_text }}
                                     </td>
                                 @else
                                     <td data-label="@lang('Gatway')">
                                         {{ __(@$deposit->gateway->name) }}
                                     </td>
                                     <td data-label="@lang('date')">
                                         {{ showDateTime(@$deposit->created_at) }}
                                     </td>
                                     <td data-label="@lang('Amount')">
                                         {{ showAmount(@$deposit->amount) }}
                                         {{ $general->cur_text }}
                                     </td>
                                 @endif
                             </tr>
                         @endforeach

                     </tbody>
                 </table>
             </div>
             <div class="col-lg-6">
                 <h3 class="table-heading"> @lang('Latest Withdraws') </h3>
                 <table class="table table--responsive--lg">
                     <thead>
                         <tr>
                             <th>@lang('Gatway')</th>
                             <th>@lang('Date')</th>
                             <th>@lang('Amount')</th>
                         </tr>
                     </thead>
                     <tbody>
                        @foreach ($withdrawals as $withdrawal)
                        <tr>

                            @if ($withdrawal->data_values)
                                <td data-label="@lang('Gatway')">
                                    {{ __(@$withdrawal->data_values->gateway) }}
                                </td>
                                <td data-label="@lang('date')">
                                    {{ showDateTime($withdrawal->data_values->date) }}
                                </td>
                                <td data-label="@lang('Amount')">
                                    {{ showAmount($withdrawal->data_values->amount) }}
                                    {{ $general->cur_text }}
                                </td>
                            @else
                                <td data-label="@lang('Gatway')">
                                    {{ __(@$withdrawal->gateway->name) }}
                                </td>
                                <td data-label="@lang('date')">
                                    {{ showDateTime($withdrawal->created_at) }}
                                </td>
                                <td data-label="@lang('Amount')">
                                    {{ showAmount($withdrawal->amount) }}
                                    {{ $general->cur_text }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </section>
