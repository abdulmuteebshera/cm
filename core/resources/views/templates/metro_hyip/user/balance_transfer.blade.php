@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="mt-120">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="custom--card">
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="form--label">@lang('Wallet')</label>
                                <select class="form--control" name="wallet">
                                    <option value="">@lang('Select a wallet')</option>
                                    <option value="deposit_wallet">@lang('Deposit Wallet') -
                                        {{ showAmount($user->deposit_wallet) }} {{ $general->cur_text }}</option>
                                    <option value="interest_wallet">@lang('Interest Wallet') -
                                        {{ showAmount($user->interest_wallet) }} {{ $general->cur_text }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Username')</label>
                                <input type="text" name="username" class="form--control findUser" required>
                                <code class="error-message"></code>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Amount') <small
                                        class="text--success">(@lang('Charge'):
                                        {{ getAmount($general->f_charge) }} {{ $general->cur_text }} +
                                        {{ getAmount($general->p_charge) }}%)</small></label>
                                <div class="input-group">
                                    <input type="number" step="any" autocomplete="off" name="amount"
                                        class="form-control form--control">
                                    <span class="input-group-text bg--base">{{ $general->cur_text }}</span>
                                </div>
                                <small><code class="calculation"></code></small>
                            </div>
                            @if (auth()->user()->ts)
                                <div class="form-group">
                                    <label for="authenticator-code" class="form--label">@lang('Google Authenticator Code')</label>
                                    <input type="text" name="authenticator_code" class="form--control" id="authenticator-code" required>
                                </div>
                            @endif
                            <div class="form-group">
                                <button type="submit" class="btn btn--base w-100">@lang('Transfer')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $('input[name=amount]').on('input', function() {
            var amo = parseFloat($(this).val());
            var calculation = amo + (parseFloat({{ $general->f_charge }}) + (amo * parseFloat(
                {{ $general->p_charge }})) / 100);
            if (calculation) {
                $('.calculation').text(calculation +
                    ' {{ $general->cur_text }} will cut from your selected wallet');
            } else {
                $('.calculation').text('');
            }
        });

        $('.findUser').on('focusout', function(e) {
            var url = '{{ route('user.findUser') }}';
            var value = $(this).val();
            var token = '{{ csrf_token() }}';

            var data = {
                username: value,
                _token: token
            }
            $.post(url, data, function(response) {
                if (response.message) {
                    $('.error-message').text(response.message);
                } else {
                    $('.error-message').text('');
                }
            });
        });
    </script>
@endpush
