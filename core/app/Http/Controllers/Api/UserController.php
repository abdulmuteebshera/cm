<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\HyipLab;
use App\Lib\StrategyPayoutService;
use App\Models\Deposit;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\GeneralSetting;
use App\Models\Invest;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $year = (int) date('Y');

        $nextWorkingDay = now()->toDateString();
        $isHoliday      = HyipLab::isHoliDay(now()->toDateTimeString(), gs());
        if ($isHoliday) {
            $nextWorkingDay = Carbon::parse(HyipLab::nextWorkingDay(24))->toDateString();
        }

        $data = [
            'user'                  => $user,
            'portfolio_value'       => $user->deposit_wallet + $user->interest_wallet,
            'total_invest'          => Invest::where('user_id', $user->id)->sum('amount'),
            'total_deposit'         => Deposit::where('user_id', $user->id)->where('status', 1)->sum('amount'),
            'total_withdrawal'      => Withdrawal::where('user_id', $user->id)->whereIn('status', [1])->sum('amount'),
            'referral_earnings'     => Transaction::where('user_id', $user->id)->where('remark', 'referral_commission')->sum('amount'),
            'pending_deposit'       => Deposit::pending()->where('user_id', $user->id)->sum('amount'),
            'pending_withdraw'      => Withdrawal::pending()->where('user_id', $user->id)->sum('amount'),
            'submitted_deposits'    => Deposit::where('status', '!=', 0)->where('user_id', $user->id)->sum('amount'),
            'successful_deposits'   => Deposit::successful()->where('user_id', $user->id)->sum('amount'),
            'rejected_deposits'     => Deposit::rejected()->where('user_id', $user->id)->sum('amount'),
            'submitted_withdrawals' => Withdrawal::where('status', '!=', 0)->where('user_id', $user->id)->sum('amount'),
            'successful_withdrawals'=> Withdrawal::approved()->where('user_id', $user->id)->sum('amount'),
            'rejected_withdrawals'  => Withdrawal::rejected()->where('user_id', $user->id)->sum('amount'),
            'invests'               => Invest::where('user_id', $user->id)->sum('amount'),
            'completed_invests'     => Invest::where('user_id', $user->id)->where('status', 0)->sum('amount'),
            'running_invests'       => Invest::where('user_id', $user->id)->where('status', 1)->sum('amount'),
            'interests'             => Transaction::where('remark', 'interest')->where('user_id', $user->id)->sum('amount'),
            'deposit_wallet_invests'=> Invest::where('user_id', $user->id)->where('wallet_type', 'deposit_wallet')->where('status', 1)->sum('amount'),
            'interest_wallet_invests'=> Invest::where('user_id', $user->id)->where('wallet_type', 'interest_wallet')->where('status', 1)->sum('amount'),
            'is_holiday'            => $isHoliday,
            'next_working_day'      => $nextWorkingDay,
            'chart_data'            => StrategyPayoutService::userReturnAnalyticsChart($user, $year)->values(),
            'strategy_charts'       => StrategyPayoutService::userStrategyChartsForDashboard($year)->values(),
            'strategy_chart_year'   => $year,
            'year_to_date_return'   => StrategyPayoutService::userYearToDateReturn($user, $year),
            'allocation'            => [
                'labels' => ['Forex', 'Indices', 'Commodities', 'Futures', 'Crypto'],
                'series' => [20, 18, 33, 15, 14],
                'colors' => ['#1989BE', '#14709a', '#47a8d4', '#7fc4e8', '#b3dff5'],
            ],
            'transactions'          => $user->transactions()->orderByDesc('id')->take(8)->get(),
        ];

        return getResponse('dashboard', 'success', 'User Dashboard', $data);
    }

    public function userInfo()
    {
        return getResponse('user_info', 'success', 'User Information', ['user' => auth()->user()]);
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->profile_complete == 1) {
            $notify[] = 'You\'ve already completed your profile';
            return response()->json([
                'remark'  => 'already_completed',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];
        $user->profile_complete = 1;
        $user->save();

        $notify[] = 'Profile completed successfully';
        return response()->json([
            'remark'  => 'profile_completed',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function kycForm()
    {
        if (auth()->user()->kv == 2) {
            $notify[] = 'Your KYC is under review';
            return response()->json([
                'remark'  => 'under_review',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        if (auth()->user()->kv == 1) {
            $notify[] = 'You are already KYC verified';
            return response()->json([
                'remark'  => 'already_verified',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $form     = Form::where('act', 'kyc')->first();
        $notify[] = 'KYC field is below';
        return response()->json([
            'remark'  => 'kyc_form',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'form' => $form->form_data,
            ],
        ]);
    }

    public function kycSubmit(Request $request)
    {
        $form           = Form::where('act', 'kyc')->first();
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        $validator = Validator::make($request->all(), $validationRule);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $userData       = $formProcessor->processFormData($request, $formData);
        $user           = auth()->user();
        $user->kyc_data = $userData;
        $user->kv       = 2;
        $user->save();

        $notify[] = 'KYC data submitted successfully';
        return response()->json([
            'remark'  => 'kyc_submitted',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);

    }

    public function depositHistory()
    {
        $deposits = auth()->user()->deposits()->with(['gateway'])->searchable(['trx'])->apiQuery();

        $notify[] = 'Deposit data';
        return response()->json([
            'remark'  => 'deposits',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'deposits' => $deposits,
            ],
        ]);
    }

    public function transactions(Request $request)
    {
        $remarks      = Transaction::distinct('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id());

        if ($request->type) {
            $type         = $request->type == 'plus' ? '+' : '-';
            $transactions = $transactions->where('trx_type', $type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }
        
        if($request->wallet_type){
            $transactions = $transactions->where('wallet_type', $request->wallet_type);
        }

        $transactions = $transactions->searchable(['trx'])->apiQuery();
        $notify[]     = 'Transactions data';

        return response()->json([
            'remark'  => 'transactions',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'transactions' => $transactions,
                'remarks'      => $remarks,
            ],
        ]);
    }

    public function submitProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];
        $user->save();

        $notify[] = 'Profile updated successfully';
        return response()->json([
            'remark'  => 'profile_updated',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function submitPassword(Request $request)
    {
        $passwordValidation = Password::min(6);
        $general            = GeneralSetting::first();
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', $passwordValidation],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password       = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = 'Password changed successfully';
            return response()->json([
                'remark'  => 'password_changed',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        } else {
            $notify[] = 'The password doesn\'t match!';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
    }
    
    public function myReferrals()
    {
        $maxLevel  = Referral::max('level');
        
        $relations = [];
        for ($label = 1; $label <= $maxLevel; $label++) {
            $relations[$label] = (@$relations[$label - 1] ? $relations[$label - 1] . '.allReferrals' : 'allReferrals');
        }

        $user = auth()->user()->load($relations);
        
        $referrals = getReferees($user, $maxLevel);
        
        return getResponse('referral_list', 'success', 'My referrals list', ['referrals' => $referrals]);
    }

    public function balanceTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wallet'   => 'required|in:deposit_wallet,interest_wallet',
            'username' => 'required',
            'amount'   => 'required|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return getResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user = auth()->user();
        if ($user->username == $request->username) {
            return getResponse('error_own_account', 'error', ['You cannot transfer balance to your own account']);
        }

        $receiver = User::where('username', $request->username)->first();
        if (!$receiver) {
            return getResponse('not_found', 'error', ['Oops! Receiver not found']);
        }

        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                return getResponse('wrong_code', 'error', ['Wrong verification code']);
            }
        }

        $general     = gs();
        $charge      = $general->f_charge + ($request->amount * $general->p_charge) / 100;
        $afterCharge = $request->amount + $charge;
        $wallet      = $request->wallet;

        if ($user->$wallet < $afterCharge) {
            return getResponse('insufficient_balance', 'error', ['You have no sufficient balance to this wallet']);
        }

        $user->$wallet -= $afterCharge;
        $user->save();

        $trx1                      = getTrx();
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = getAmount($afterCharge);
        $transaction->charge       = $charge;
        $transaction->trx_type     = '-';
        $transaction->trx          = $trx1;
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'balance_transfer';
        $transaction->details      = 'Balance transfer to ' . $receiver->username;
        $transaction->post_balance = getAmount($user->$wallet);
        $transaction->save();

        $receiver->deposit_wallet += $request->amount;
        $receiver->save();

        $trx2                      = getTrx();
        $transaction               = new Transaction();
        $transaction->user_id      = $receiver->id;
        $transaction->amount       = getAmount($request->amount);
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->trx          = $trx2;
        $transaction->wallet_type  = 'deposit_wallet';
        $transaction->remark       = 'balance_received';
        $transaction->details      = 'Balance received from ' . $user->username;
        $transaction->post_balance = getAmount($user->deposit_wallet);
        $transaction->save();

        notify($user, 'BALANCE_TRANSFER', [
            'amount'        => showAmount($request->amount),
            'charge'        => showAmount($charge),
            'wallet_type'   => keyToTitle($wallet),
            'post_balance'  => showAmount($user->$wallet),
            'user_fullname' => $receiver->fullname,
            'username'      => $receiver->username,
            'trx'           => $trx1,
        ]);

        notify($receiver, 'BALANCE_RECEIVE', [
            'wallet_type'  => 'Deposit wallet',
            'amount'       => showAmount($request->amount),
            'post_balance' => showAmount($receiver->deposit_wallet),
            'sender'       => $user->username,
            'trx'          => $trx2,
        ]);

        return getResponse('balance_transfer', 'success', ['Balance transferred successfully']);
    }



    public function getDeviceToken(Request $request){

        $validator = Validator::make($request->all(), [
            'token'=> 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if($deviceToken){
            $notify[] = 'Already exists';
            return response()->json([
                'remark'=>'get_device_token',
                'status'=>'success',
                'message'=>['success'=>$notify],
            ]);
        }

        $deviceToken = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token = $request->token;
        $deviceToken->is_app = 1;
        $deviceToken->save();

        $notify[] = 'Token save successfully';
        return response()->json([
            'remark'=>'get_device_token',
            'status'=>'success',
            'message'=>['success'=>$notify],
        ]);
    }

}
