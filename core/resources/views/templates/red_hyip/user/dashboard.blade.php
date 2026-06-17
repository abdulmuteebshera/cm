@extends($activeTemplate . 'layouts.master')
@section('content')
    <section>
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if ($user->deposit_wallet <= 0 && $user->interest_wallet <= 0)
                    <div class="d-flex align-items-center alert border border--danger bg--dark" role="alert">
                        <div class="alert__icon text--danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('Empty Balance')</span><br>
                            <small>
                                <i>@lang('Your balance is empty. Please make')
                                    <a href="{{ route('user.deposit.index') }}" class="text--base">
                                        @lang('deposit')
                                    </a>
                                    @lang('for your next investment.')
                                </i>
                            </small>
                        </p>
                    </div>
                @endif

                @if ($user->deposits->where('status', 1)->count() == 1 && !$user->invests->count())
                    <div class="d-flex align-items-center alert border border--success bg--dark" role="alert">
                        <div class="alert__icon text--success">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('First Deposit')</span><br>
                            <small>
                                <i>
                                    <span class="fw-bold">@lang('Congratulations!')</span>
                                    @lang('You\'ve made your first deposit successfully. Go to')
                                    <a href="{{ route('plan') }}" class="text--base">
                                        @lang('investment plan')
                                    </a>
                                    @lang('page and invest now')
                                </i>
                            </small>
                        </p>
                    </div>
                @endif

                @if ($pendingWithdrawals)
                    <div class="alert d-flex align-items-center border border--primary bg--dark" role="alert">
                        <div class="alert__icon text--primary"><i class="fas fa-spinner"></i>
                        </div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('Withdrawal Pending')</span><br>
                            <small><i>@lang('Total') {{ showAmount($pendingWithdrawals) }} {{ __($general->cur_text) }}
                                    @lang('withdrawal request is pending. Please wait for admin approval. The amount will send to the account which you\'ve provided. See') <a href="{{ route('user.withdraw.history') }}"
                                        class="text--base">@lang('withdrawal history')</a></i></small>
                        </p>
                    </div>
                @endif

                @if ($pendingDeposits)
                    <div class="alert d-flex align-items-center border border--primary bg--dark" role="alert">
                        <div class="alert__icon text--primary"><i class="fas fa-spinner"></i>
                        </div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('Deposit Pending')</span><br>
                            <small><i>@lang('Total') {{ showAmount($pendingDeposits) }} {{ __($general->cur_text) }}
                                    @lang('deposit request is pending. Please wait for admin approval. See') <a href="{{ route('user.deposit.history') }}"
                                        class="text--base">@lang('deposit history')</a></i></small>
                        </p>
                    </div>
                @endif

                @if (!$user->ts)
                    <div class="alert d-flex align-items-center border border--warning bg--dark" role="alert">
                        <div class="alert__icon text--warning">
                            <i class="fas fa-user-lock"></i>
                        </div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('2FA Authentication')</span><br>
                            <small><i>@lang('To keep safe your account, Please enable') <a href="{{ route('user.twofactor') }}"
                                        class="text--base">@lang('2FA')</a> @lang('security').</i>
                                @lang('It will make secure your account and balance.')</small>
                        </p>
                    </div>
                @endif

                @if ($isHoliday)
                    <div class="alert d-flex align-items-center border border--info bg--dark" role="alert">
                        <div class="alert__icon text--info">
                            <i class="fas fa-toggle-off"></i>
                        </div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('Holiday')</span><br>
                            <small><i>@lang('Today is holiday on this system. You\'ll not get any interest today from this system. Also you\'re unable to make withdrawal request today.') <br> @lang('The next working day is coming after') <span id="counter"
                                        class="fw-bold text--primary fs--15px"></span></i></small>
                        </p>
                    </div>
                @endif

                @if ($user->kv == 0)
                    <div class="alert d-flex align-items-center border border--info bg--dark" role="alert">
                        <div class="alert__icon text--info"><i class="fas fa-file-signature"></i>
                        </div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('KYC Verification Required')</span><br>
                            <small><i>@lang('Please submit the required KYC information to verify yourself. Otherwise, you couldn\'t make any withdrawal requests to the system.') <a href="{{ route('user.kyc.form') }}"
                                        class="text--base">@lang('Click here')</a> @lang('to submit KYC information').</i></small>
                        </p>
                    </div>
                @elseif($user->kv == 2)
                    <div class="alert d-flex align-items-center border border--warning bg--dark" role="alert">
                        <div class="alert__icon text--warning"><i class="fas fa-user-check"></i></div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('KYC Verification Pending')</span><br>
                            <small><i>@lang('Your submitted KYC information is pending for admin approval. Please wait till that.') <a href="{{ route('user.kyc.data') }}"
                                        class="text--base">@lang('Click here')</a> @lang('to see your submitted information')</i></small>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="row gy-4 justify-content-center">
            <div class="col-xxl-4 col-xl-6 col-sm-6">
                <a href="{{ route('user.transactions') }}?wallet_type=deposit_wallet" class="dashboard-card__link">
                    <div class="dashboard-card">
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text">@lang('Deposit Wallet Balance')</span>
                            <h4 class="dashboard-card__amount">{{ showAmount($user->deposit_wallet) }}
                                {{ __($general->cur_text) }}</h4>
                        </div>
                        <div class="dashboard-card__icon">
                            <i class="las la-dollar-sign"></i>
                        </div>

                    </div>
                </a>
            </div>

            <div class="col-xxl-4 col-xl-6 col-sm-6">
                <a href="{{ route('user.transactions') }}?wallet_type=interest_wallet" class="dashboard-card__link">
                    <div class="dashboard-card">
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text">@lang('Interest Wallet Balance')</span>
                            <h4 class="dashboard-card__amount">{{ showAmount($user->interest_wallet) }}
                                {{ __($general->cur_text) }}</h4>
                        </div>
                        <div class="dashboard-card__icon">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-4 col-xl-6 col-sm-6">
                <a href="{{ route('user.invest.statistics') }}" class="dashboard-card__link">
                    <div class="dashboard-card">
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text"> @lang('Total Invest Amount') </span>
                            <h4 class="dashboard-card__amount">{{ showAmount($totalInvest) }} {{ __($general->cur_text) }}
                            </h4>
                        </div>
                        <div class="dashboard-card__icon">
                            <i class="fas fa-chart-area"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-4 col-xl-6 col-sm-6">
                <a href="{{ route('user.deposit.history') }}" title="@lang('View All')" class="dashboard-card__link">
                    <div class="dashboard-card">
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text">@lang('Total Deposit Amount') </span>
                            <h4 class="dashboard-card__amount">
                                {{ showAmount($user->deposits->where('status', 1)->sum('amount')) }}
                                {{ __($general->cur_text) }}
                        </div>
                        <div class="dashboard-card__icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>

                    </div>
                </a>
            </div>

            <div class="col-xxl-4 col-xl-6 col-sm-6">
                <a href="{{ route('user.withdraw.history') }}" class="dashboard-card__link">
                    <div class="dashboard-card">
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text">@lang('Total Withdraw Amount')</span>
                            <h4 class="dashboard-card__amount">
                                {{ showAmount($user->withdrawals->where('status', 1)->sum('amount')) }}
                                {{ __($general->cur_text) }}</h4>
                        </div>
                        <div class="dashboard-card__icon">
                            <i class="las la-cloud-download-alt"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-4 col-xl-6 col-sm-6">
                <a href="{{ route('user.transactions') }}?remark=referral_commission" class="dashboard-card__link">
                    <div class="dashboard-card">
                        <div class="dashboard-card__content">
                            <span class="dashboard-card__text">@lang('Referral Earning')</span>
                            <h4 class="dashboard-card__amount">{{ showAmount(0.0) }} {{ __($general->cur_text) }}</h4>
                        </div>
                        <div class="dashboard-card__icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <div class="dashboard-table mt-5">
            <h4 class="dashboard-table-title mb-30">@lang('My Transaction')</h4>
            <table class="table table--responsive--xl">
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
                            <td><span class="text-primary trx">{{ $trx->trx }}</span></td>

                            <td>
                                @if ($trx->trx_type == '+')
                                    <span class="text-success">+
                                        {{ $general->cur_sym }}{{ showAmount($trx->amount) }}</span>
                                @else
                                    <span class="text-danger">-
                                        {{ $general->cur_sym }}{{ showAmount($trx->amount) }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($trx->wallet_type == 'deposit_wallet')
                                    <span class="badge badge--info">@lang('Deposit Wallet')</span>
                                @else
                                    <span class="badge badge--primary">@lang('Interest Wallet')</span>
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
                                @lang('No Transaction Found')
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection


@push('style')
<style>
@media (max-width: 320.98px) {
    .trx {
        font-size: 11px;
    }
}
</style>
@endpush
