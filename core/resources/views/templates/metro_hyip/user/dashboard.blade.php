@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="mt-3 mb-60">
        <div class="row notice"></div>
        <div class="row justify-content-center mb-3">
            <div class="col-md-12">
                @if ($user->deposit_wallet <= 0 && $user->interest_wallet <= 0)
                    <div class="alert border border--danger" role="alert">
                        <div class="d-flex align-items-center text--warning alert-box">
                            <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="alert-box__content">
                                <span class="fw-bold">@lang('Empty Balance')</span><br>
                                <p class="alert__message">
                                    <small>
                                        <i>@lang('Your balance is empty. Please make')
                                            <a class="text--base-two" href="{{ route('user.deposit.index') }}"
                                                class="link-color">@lang('deposit')</a> @lang('for your next investment.')</i>
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($user->deposits->where('status', 1)->count() == 1 && !$user->invests->count())
                    <div class="alert border border--primary" role="alert">
                        <div class="d-flex align-items-center text--warning alert-box">
                            <span class="icon"><i class="fas fa-check"></i></span>
                            <div class="alert-box__content">
                                <span class="fw-bold">@lang('First Deposit')</span><br>
                                <p class="alert__message">
                                    <small><i><span class="fw-bold">@lang('Congratulations!')</span> @lang('You\'ve made your first deposit successfully. Go to')
                                            <a href="{{ route('plan') }}" class="text--base-two">@lang('investment plan')</a>
                                            @lang('page and invest now')</i>
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($pendingWithdrawals)
                    <div class="alert border border--warning" role="alert">
                        <div class="d-flex align-items-center text--warning alert-box">
                            <span class="icon"><i class="fas fa-spinner"></i></span>
                            <div class="alert-box__content">
                                <span class="fw-bold">@lang('Withdrawal Pending')</span><br>
                                <p class="alert__message">
                                    <small><i>@lang('Total') {{ showAmount($pendingWithdrawals) }}
                                            {{ $general->cur_text }}
                                            @lang('withdrawal request is pending. Please wait for admin approval. The amount will send to the account which you\'ve provided. See')
                                            <a class="text--base-two" href="{{ route('user.withdraw.history') }}"
                                                class="link-color">@lang('withdrawal history')</a></i></small>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($pendingDeposits)
                    <div class="alert border border--warning" role="alert">
                        <div class="d-flex align-items-center text--warning alert-box">
                            <span class="icon"><i class="fas fa-spinner"></i></span>
                            <div class="alert-box__content">
                                <span class="fw-bold">@lang('Deposit Pending')</span><br>
                                <p class="alert__message">
                                    <small><i>@lang('Total') {{ showAmount($pendingDeposits) }} {{ $general->cur_text }}
                                            @lang('deposit request is pending. Please wait for admin approval. See') <a class="text--base-two" href="{{ route('user.deposit.history') }}">@lang('deposit history')</a></i>
                                        </small>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                @if (!$user->ts)
                    <div class="alert border border--warning" role="alert">
                        <div class="d-flex align-items-center text--warning alert-box">
                            <span class="icon"> <i class="fas fa-user-lock"></i> </span>
                            <div class="alert-box__content">
                                <span class="fw-bold">@lang('2FA Authentication')</span><br>
                                <p class="alert__message">
                                    <small><i>@lang('To keep safe your account, Please enable') <a href="{{ route('user.twofactor') }}"
                                                class="link-color">@lang('2FA')</a> @lang('security').</i>
                                        @lang('It will make secure your account and balance.')</small>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($isHoliday)
                    <div class="alert border border--violet" role="alert">
                        <div class="d-flex align-items-center text--violet"><i class="fas fa-toggle-off"></i>
                            <span class="fw-bold mx-3">@lang('Holiday')</span><br>
                        </div>
                        <p>
                            <small><i>@lang('Today is holiday on this system. You\'ll not get any interest today from this system. Also you\'re unable to make withdrawal request today.') <br> @lang('The next working day is coming after') <span id="counter"
                                        class="fw-bold text--violet fs--15px"></span></i></small>
                        </p>
                    </div>
                @endif
                @if ($user->kv == 0)
                    <div class="alert border border--info" role="alert">
                        <div class="d-flex align-items-center text--info"><i class="fas fa-file-signature"></i>
                            <span class="fw-bold mx-3">@lang('KYC Verification Required')</span><br>
                        </div>
                        <p>
                            <small><i>@lang('Please submit the required KYC information to verify yourself. Otherwise, you couldn\'t make any withdrawal requests to the system.') <a href="{{ route('user.kyc.form') }}"
                                        class="text--base-two">@lang('Click here')</a> @lang('to submit KYC information').</i></small>
                        </p>
                    </div>
                @elseif($user->kv == 2)
                    <div class="alert border border--warning" role="alert">
                        <div class="d-flex align-items-center text--warning"><i class="fas fa-user-check"></i>
                            <span class="fw-bold mx-3">@lang('KYC Verification Pending')</span><br>
                        </div>
                        <p>
                            <small><i>@lang('Your submitted KYC information is pending for admin approval. Please wait till that.') <a href="{{ route('user.kyc.data') }}"
                                        class="text--base-two">@lang('Click here')</a> @lang('to see your submitted information')</i></small>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="row gy-4 justify-content-center">
            <div class="col-xxl-4 col-xl-6 col-sm-6">
                <div class="dashboard-card">
                    <div class="dashboard-card__shape"></div>
                    <div class="dashboard-card__header">
                        <span class="dashboard-card__header-icon style-four">
                            <i class="fas fa-wallet"></i>
                        </span>
                        <div class="dashboard-card__header-content">
                            <h6 class="dashboard-card__header-title"> @lang('Total Deposit') </h6>
                            <span class="dashboard-card__header-currency"> {{ showAmount($successfulDeposits) }}
                                {{ $general->cur_text }} </span>
                        </div>
                    </div>
                    <div class="dashboard-card__item">
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text"> @lang('Submitted') </span>
                            <h4 class="dashboard-card__amount">
                                {{ $general->cur_sym }}{{ showAmount($submittedDeposits) }} </h4>
                        </div>
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text"> @lang('Pending') </span>
                            <h4 class="dashboard-card__amount"> {{ $general->cur_sym }}{{ showAmount($pendingDeposits) }}
                            </h4>
                        </div>
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text"> @lang('Rejected') </span>
                            <h4 class="dashboard-card__amount">
                                {{ $general->cur_sym }}{{ showAmount($rejectedDeposits) }} </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-6 col-sm-6">
                <div class="dashboard-card">
                    <div class="dashboard-card__shape"></div>
                    <div class="dashboard-card__header">
                        <span class="dashboard-card__header-icon style-two">
                            <i class="fas fa-coins"></i>
                        </span>
                        <div class="dashboard-card__header-content">
                            <h6 class="dashboard-card__header-title"> @lang('Total Withdraw') </h6>
                            <span class="dashboard-card__header-currency"> {{ showAmount($successfulWithdrawals) }}
                                {{ $general->cur_text }} </span>
                        </div>
                    </div>
                    <div class="dashboard-card__item">
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text"> @lang('Submitted') </span>
                            <h4 class="dashboard-card__amount">
                                {{ $general->cur_sym }}{{ showAmount($submittedWithdrawals) }} </h4>
                        </div>
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text"> @lang('Pending') </span>
                            <h4 class="dashboard-card__amount">
                                {{ $general->cur_sym }}{{ showAmount($pendingWithdrawals) }} </h4>
                        </div>
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text"> @lang('Rejected') </span>
                            <h4 class="dashboard-card__amount">
                                {{ $general->cur_sym }}{{ showAmount($rejectedWithdrawals) }} </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-6 col-sm-6">
                <div class="dashboard-card">
                    <div class="dashboard-card__shape"></div>
                    <div class="dashboard-card__header">
                        <span class="dashboard-card__header-icon">
                            <i class="fas fa-funnel-dollar"></i>
                        </span>
                        <div class="dashboard-card__header-content">
                            <h6 class="dashboard-card__header-title"> @lang('Total Investments') </h6>
                            <span class="dashboard-card__header-currency"> {{ showAmount($invests) }}
                                {{ $general->cur_text }} </span>
                        </div>
                    </div>
                    <div class="dashboard-card__item">
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text"> @lang('Completed') </span>
                            <h4 class="dashboard-card__amount">
                                {{ $general->cur_sym }}{{ showAmount($completedInvests) }} </h4>
                        </div>
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text"> @lang('Running') </span>
                            <h4 class="dashboard-card__amount"> {{ $general->cur_sym }}{{ showAmount($runningInvests) }}
                            </h4>
                        </div>
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text"> @lang('Interests') </span>
                            <h4 class="dashboard-card__amount"> {{ $general->cur_sym }}{{ showAmount($interests) }} </h4>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row gy-4 pt-4 justify-content-center">
            <div class="col-xxl-4 col-xl-6 col-md-6">
                <div class="dashboard-item">
                    <h5 class="dashboard-item__title">
                        @lang('My Investment Progress')
                    </h5>
                    @php
                        $completedPercent = $totalInvest ? ($completedInvests / $totalInvest) * 100 : 0;
                        $runningPercent = $totalInvest ? ($runningInvests / $totalInvest) * 100 : 0;
                    @endphp
                    <div class="progress-wrapper mb-70">
                        <div class="progress-basic">
                            <div class="mb-3">
                                <div class="investment-wrapper">
                                    <div class="d-flex align-items-center">
                                        <span class="investment-wrapper__icon">
                                            <i class="fas fa-funnel-dollar"></i>
                                        </span>
                                        <div class="investment-wrapper__rate">
                                            <h6 class="investment-wrapper__title">
                                                @lang('Total Investment')
                                            </h6>
                                            <span class="investment-wrapper__interest"> 100% @lang('investment is')
                                                {{ showAmount($totalInvest) }} {{ __($general->cur_text) }} </span>
                                        </div>
                                    </div>

                                </div>
                                <div class="progress mb-2">
                                    <div class="progress-bar progress-basic-1" data-wow-duration="1s"
                                        style="width: {{ $totalInvest }}%;" data-wow-delay="0.5s" role="progressbar"
                                        aria-valuenow="{{ $totalInvest }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="investment-wrapper">
                                    <div class="d-flex align-items-center">
                                        <span class="investment-wrapper__icon style-two">
                                            <i class="fas fa-funnel-dollar"></i>
                                        </span>
                                        <div class="investment-wrapper__rate">
                                            <h6 class="investment-wrapper__title">
                                                @lang('Complate Investment')
                                            </h6>
                                            <span class="investment-wrapper__interest">
                                                {{ showAmount($completedPercent) }}% @lang('of')
                                                {{ showAmount($completedInvests) }} {{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>

                                </div>
                                <div class="progress mb-2">
                                    <div class="progress-bar progress-basic-2" data-wow-duration="1s"
                                        style="width: {{ $completedPercent }}%;" data-wow-delay="0.5s"
                                        role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            <div>

                                <div class="investment-wrapper">
                                    <div class="d-flex align-items-center">
                                        <span class="investment-wrapper__icon style-three">
                                            <i class="fas fa-funnel-dollar"></i>
                                        </span>
                                        <div class="investment-wrapper__rate">
                                            <h6 class="investment-wrapper__title">
                                                @lang('Running Investment')
                                            </h6>
                                            <span class="investment-wrapper__interest"> {{ showAmount($runningPercent) }}%
                                                @lang('of') {{ showAmount($runningInvests) }}
                                                {{ __($general->cur_text) }}
                                            </span>
                                        </div>
                                    </div>

                                </div>

                                <div class="progress mb-2">
                                    <div class="progress-bar progress-basic-3" data-wow-duration="1s"
                                        style="width: {{ $runningPercent }}%;" data-wow-delay="0.5s" role="progressbar"
                                        aria-valuenow="{{ $runningPercent }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-4 col-xl-6 col-md-6">
                <div class="dashboard-item">
                    <h5 class="dashboard-item__title">
                        @lang('Invested By Wallets')
                    </h5>
                    @php
                        $totalInvestByWallet = $depositWalletInvests ?? ($interestWalletInvests ? $interestWalletInvests : 0);
                        $depositWallet = $totalInvestByWallet ? ($depositWalletInvests / $totalInvestByWallet) * 100 : 0;
                        $interestWallet = $totalInvestByWallet ? ($interestWalletInvests / $totalInvestByWallet) * 100 : 0;
                    @endphp
                    <div class="dashboard-item__investment">
                        <span class="dashboard-item__investment-title"> @lang('Total Invested By Wallets') </span>
                        <h3 class="dashboard-item__investment-number"> {{ showAmount($totalInvestByWallet) }}
                            {{ __($general->cur_text) }} </h3>
                    </div>
                    <div class="d-flex align-items-start justify-content-center">
                        <div class="dashboard-item__wallet">
                            <span class="dashboard-item__wallet-title"> @lang('Deposit Wallet') </span>
                            <h4 class="dashboard-item__wallet-number">
                                {{ $general->cur_sym }}{{ showAmount($depositWalletInvests) }} </h4>
                            <span class="dashboard-item__wallet-persentage"><span class="dashboard-item__wallet-icon"><i
                                        class="fas fa-long-arrow-alt-up"></i></span> {{ $depositWallet }}%</span>
                        </div>
                        <div class="dashboard-item__wallet two">
                            <span class="dashboard-item__wallet-title"> @lang('Interest Wallet') </span>
                            <h4 class="dashboard-item__wallet-number">
                                {{ $general->cur_sym }}{{ showAmount($interestWalletInvests) }} </h4>
                            <span class="dashboard-item__wallet-persentage"><span class="dashboard-item__wallet-icon"><i
                                        class="fas fa-long-arrow-alt-up"></i></span> {{ $interestWallet }}%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-6 col-md-6">
                <div class="dashboard-item">
                    <h4 class="dashboard-item__title"> @lang('Profit Paying History')</h4>
                    @php
                        $investPaidHistory = $user?->invests->where('status', 1);
                        $shouldPay = 0;
                        $paid = 0;
                        foreach ($investPaidHistory as $value) {
                            $shouldPay += $value->should_pay;
                            $paid += $value->paid;
                        }
                        $total = $shouldPay + $paid;
                        $paidPercent = ($total * $paid) / 100;
                    @endphp
                    <div class="dashboard-item__pay">
                        <span class="dashboard-item__investment-title">@lang('Should Pay')</span>
                        <h6 class="dashboard-item__pay-number">{{ $general->cur_sym }}{{ showAmount($shouldPay) }}</h6>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="price"> @lang('Paid') ({{ showAmount($paidPercent) }}%) /
                            {{ $general->cur_sym }}{{ showAmount($paid) }}</span>
                        <span class="price"> {{ $general->cur_sym }}{{ showAmount($total) }} </span>
                    </div>
                    <div class="progress-basic">
                        <div class="progress mb-3">
                            <div class="progress-bar progress-basic-1" data-wow-duration="1s"
                                style="width: {{ $paidPercent }}%" data-wow-delay="0.5s" role="progressbar"
                                aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <p class="dashboard-item__desc">* @lang('This statistic is showing excluding lifetime investment'). </p>
                </div>
            </div>
        </div>

        <div class="pt-4 table-section">
            <h4>@lang('My Latest Transactions')</h4>
            <div class="row gy-4">
                <div class="col-lg-12">
                    <table class="table style-two table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('Date')</th>
                                <th>@lang('Transaction ID')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Wallet')</th>
                                <th>@lang('Details')</th>
                                <th>@lang('Post Balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>
                                        {{ showDatetime($trx->created_at, 'd/m/Y') }}
                                    </td>
                                    <td><span class="text-primary">{{ $trx->trx }}</span></td>

                                    <td>
                                        @if ($trx->trx_type == '+')
                                            <span class="text--success">+
                                                {{ __($general->cur_sym) }}{{ showAmount($trx->amount) }}</span>
                                        @else
                                            <span class="text--danger">-
                                                {{ __($general->cur_sym) }}{{ showAmount($trx->amount) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($trx->wallet_type == 'deposit_wallet')
                                            <span class="badge badge--info">@lang('Deposit Wallet')</span>
                                        @else
                                            <span class="badge badge--warning">@lang('Interest Wallet')</span>
                                        @endif
                                    </td>
                                    <td>{{ $trx->details }}</td>
                                    <td><span>
                                            {{ $general->cur_sym }}{{ showAmount($trx->post_balance) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">
                                        {{ __('No Transaction Found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        'use strict';
        (function($) {
            @if ($isHoliday)
                function createCountDown(elementId, sec) {
                    var tms = sec;
                    var x = setInterval(function() {
                        var distance = tms * 1000;
                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        var days = `<span>${days}d</span>`;
                        var hours = `<span>${hours}h</span>`;
                        var minutes = `<span>${minutes}m</span>`;
                        var seconds = `<span>${seconds}s</span>`;
                        document.getElementById(elementId).innerHTML = days + ' ' + hours + " " + minutes +
                            " " + seconds;
                        if (distance < 0) {
                            clearInterval(x);
                            document.getElementById(elementId).innerHTML = "COMPLETE";
                        }
                        tms--;
                    }, 1000);
                }

                createCountDown('counter', {{ \Carbon\Carbon::parse($nextWorkingDay)->diffInSeconds() }});
            @endif
        })(jQuery);
    </script>
@endpush
