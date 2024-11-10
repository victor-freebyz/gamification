<?php

namespace App\Http\Controllers;

use App\Helpers\PaystackHelpers;
use App\Helpers\Sendmonny;
use App\Helpers\SystemActivities;
use App\Mail\GeneralMail;
use App\Models\BankInformation;
use App\Models\PaymentTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wallet $wallet)
    {
        //
    }

    public function fund()
    {
        // $balance = '';
        // if(walletHandler() == 'sendmonny'){
        //     $balance = Sendmonny::getUserBalance(GetSendmonnyUserId(), accessToken());
        // }
        // $location = PaystackHelpers::getLocation();
        return  view('user.wallet.fund');
    }


    public function withdraw()
    {
        return  view('user.wallet.withdraw');
    }

    public function storeFund(Request $request)
    {

        $baseCurrency = auth()->user()->wallet->base_currency;

        if($baseCurrency == 'GHS'){
            $paymentOption = 'mobilemoneyghana';

        }elseif($baseCurrency == 'KES'){
            $paymentOption = 'mpesa';

        }elseif($baseCurrency == 'RWF'){
            $paymentOption = 'mobilemoneyrwanda';

        }elseif($baseCurrency == 'TZS'){
            $paymentOption = 'mobilemoneytanzania';

        }elseif($baseCurrency == 'MWK'){
            $paymentOption = 'mobilemoneymalawi';
           
        }else{
            $paymentOption = null;
        }
            
            $amount = amountCalculator($request->balance);
            $ref = Str::random(16);

            $payload = [
                'tx_ref' => $ref,
                'amount'=> $amount,
                'currency'=> $baseCurrency, //"USD",
                'redirect_url'=> url('flutterwave/wallet/top'),
                'payment_options'=> 'card ,'. $paymentOption,
                'meta'=> [
                    'consumer_id' => auth()->user()->id,
                    'consumer_mac'=> ''
                ],
                'customer'=> [
                    'email'=> auth()->user()->email,
                    'phonenumber'=> auth()->user()->phone,
                    'name'=> auth()->user()->name,
                ],
                'customizations'=>[
                    'title'=> "Wallet Top Up",
                    // 'logo'=> "http://www.piedpiper.com/app/themes/joystick-v27/images/logo.png"
                ] 
            ];
            $url = flutterwavePaymentInitiation($payload)['data']['link'];

            // $url = PaystackHelpers::initiateTrasaction($ref, $amount, '/wallet/topup');
             //Admin Transaction Tablw
             PaymentTransaction::create([
                'user_id' => auth()->user()->id,
                'campaign_id' => '1',
                'reference' => $ref,
                'amount' => $amount,
                'status' => 'unsuccessful',
                'currency' => $baseCurrency,
                'channel' => 'flutterwave',
                'type' => 'wallet_topup',
                'description' => 'Wallet Top Up',
                'tx_type' => 'Credit',
                'user_type' => 'regular'
            ]);
 
            return redirect($url);


        

        // if($baseCurrency == 'Naira'){
        //     $ref = time();

        //     $percent = 3/100 * $request->balance;
        //     $amount = $request->balance + $percent;

        //     $payload = [
        //         'tx_ref' => time(),
        //         'amount'=> $amount,
        //         'currency'=> "NGN",
        //         'redirect_url'=> url('flutterwave/wallet/top'),
        //         'meta'=> [
        //             'consumer_id' => auth()->user()->id,
        //             'consumer_mac'=> ''
        //         ],
        //         'customer'=> [
        //             'email'=> auth()->user()->email,
        //             'phonenumber'=> auth()->user()->phone,
        //             'name'=> auth()->user()->name,
        //         ],
        //         'customizations'=>[
        //             'title'=> "Wallet Top Up",
        //             'logo'=> "http://www.piedpiper.com/app/themes/joystick-v27/images/logo.png"
        //         ] 
        //     ];
            // $url = flutterwavePaymentInitiation($payload)['data']['link'];
    
            // $url = initiateTrasaction($ref, $amount, '/wallet/topup');
            
            // paymentTrasanction(auth()->user()->id, '1', $ref, $request->balance, 'unsuccessful', 'wallet_topup', 'Wallet Topup', 'Payment_Initiation', 'regular');
            
            // return redirect($url);


        
        
        
        
        //     if($baseCurrency == 'GHS'){

        //     return 'GHS';
        
        // }else{

            // $curLocation = currentLocation();
            
            // if($curLocation == 'Nigeria'){
            //     return back()->with('error', 'You are not allowed to use this feature. Kindly top up with your Virtual Account.');
            // }

        //     $percent = 5/100 * $request->balance;
        //     $amount = $request->balance + $percent + 0.4;
        //     $ref = time();

        // if($request->method == 'flutterwave'){

       
        //     $payload = [
        //         'tx_ref' => Str::random(16),
        //         'amount'=> $amount,
        //         'currency'=> "USD",
        //         'redirect_url'=> url('flutterwave/wallet/top'),
        //         'payment_options'=> "card, mobilemoneyghana",
        //         'meta'=> [
        //             'consumer_id' => auth()->user()->id,
        //             'consumer_mac'=> ''
        //         ],
        //         'customer'=> [
        //             'email'=> auth()->user()->email,
        //             'phonenumber'=> auth()->user()->phone,
        //             'name'=> auth()->user()->name,
        //         ],
        //         'customizations'=>[
        //             'title'=> "Wallet Top Up",
        //             // 'logo'=> "http://www.piedpiper.com/app/themes/joystick-v27/images/logo.png"
        //         ] 
        //     ];
        //     $url = flutterwavePaymentInitiation($payload)['data']['link'];

        //     // $url = PaystackHelpers::initiateTrasaction($ref, $amount, '/wallet/topup');
        //      //Admin Transaction Tablw
        //      PaymentTransaction::create([
        //         'user_id' => auth()->user()->id,
        //         'campaign_id' => '1',
        //         'reference' => $ref,
        //         'amount' => $amount,
        //         'status' => 'unsuccessful',
        //         'currency' => 'USD',
        //         'channel' => 'flutterwave',
        //         'type' => 'wallet_topup',
        //         'description' => 'Wallet Top Up',
        //         'tx_type' => 'Credit',
        //         'user_type' => 'regular'
        //     ]);

        //     //PaystackHelpers::paymentTrasanction(auth()->user()->id, '1', $ref, $request->balance, 'unsuccessful', 'wallet_topup', 'Wallet Topup', 'Credit', 'Payment_Initiation', 'regular');
            
        //     return redirect($url);
        // }else{

        //     $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        //     $redirectUrl = route('stripe.checkout.success').'?session_id={CHECKOUT_SESSION_ID}';
        //     $ref = time();
        //     $response =  $stripe->checkout->sessions->create([
        //         'success_url' => $redirectUrl,
        //         'cancel_url' => url('cancel/transaction/'.$ref),
        //         'customer_email' => auth()->user()->email,
        //         'payment_method_types' => ['link', 'card'],
        //         'locale' => 'auto',
        //         'client_reference_id' => $ref,
        //         'line_items' => [
        //             [
        //                 'price_data'  => [
        //                     'product_data' => [
        //                         'name' => 'Freebyz TopUp',
        //                     ],
        //                     'unit_amount'  => 100 * $amount,
        //                     'currency'     => 'USD',
        //                 ],
        //                 'quantity'    => 1
        //             ],
        //         ],
        //         'mode' => 'payment',
        //         'allow_promotion_codes' => true,
        //         'expires_at' => time() + 3600,
                
        //         // 'automatic_payment_methods' => ['enabled' => true],
        //     ]);

        //     PaymentTransaction::create([
        //         'user_id' => auth()->user()->id,
        //         'campaign_id' => '1',
        //         'reference' => $ref,
        //         'amount' => $request->balance,
        //         'status' => 'unsuccessful',
        //         'currency' => 'USD',
        //         'channel' => 'stripe',
        //         'type' => 'wallet_topup',
        //         'description' => 'Wallet Top Up',
        //         'tx_type' => 'Credit',
        //         'user_type' => 'regular'
        //     ]);
  
        //     return redirect($response['url']);
        // }  
        // }
        
    }

    public function stripeCheckoutSuccess(Request $request){
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
  
        $session = $stripe->checkout->sessions->retrieve($request->session_id);

        if($session['payment_status'] == 'paid' && $session['status'] == 'complete'){
            
            // $amount = $session['amount_total']/10;
            // $percent = 2.90/100 * $amount;
            // $formatedAm = $percent;
            // $newamount = $amount - $formatedAm; //verify transaction
            // $creditAmount = $newamount / 100;

            $trx = PaymentTransaction::where('reference', $session['client_reference_id'])->first();
            if($trx){
                $wallet = Wallet::where('user_id', auth()->user()->id)->first();
                $wallet->usd_balance += $trx->amount;
                $wallet->save();

                $trx->status = 'successful';
                $trx->save();
            }
            return redirect()->route('fund')->with('success', 'Payment successful and you wallet credited.');
        }else{
            return redirect('wallet/fund');
        }

        
    //    return info($session);
  
        // return redirect()->route('stripe.index')
        //                  ->with('success', 'Payment successful.');
    }

    public function cancelUrl($ref){
        PaymentTransaction::where('reference', $ref)->delete();

        return redirect()->route('fund')
        ->with('error', 'Payment Cancelled');
    }

    public function capturePaypal(){
        $url = request()->fullUrl();
        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);

        $id = $params['token'];

          $response = capturePaypalPayment($id);

          $user = Auth::user();
        if($response['status'] == 'COMPLETED'){

            //$ref = $response['purchase_units'][0]['reference_id'];
         
            // $sellerReceivableBreakdown = $response['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown'];

            // Access individual values
            // $grossAmount = $sellerReceivableBreakdown['gross_amount']['value'];
            // $paypalFee = $sellerReceivableBreakdown['paypal_fee']['value'];
            // $netAmount = $sellerReceivableBreakdown['net_amount']['value'];

            // $currency = $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];

            // $data['ref'] = $ref;
            // $data['currency'] = $currency;
            // $data['net'] = $netAmount;
            // $data['amount'] = $grossAmount;
            // $data['fee'] = $paypalFee;

            $update = PaymentTransaction::where('reference', $response['id'])->first();
            $update->status = 'successful';
            $update->reference = $response['purchase_units'][0]['reference_id'];
            $update->save();

            $wallet = Wallet::where('user_id', auth()->user()->id)->first();
            $wallet->usd_balance += $update->amount;
            $wallet->save();
           
            activityLog(auth()->user(), 'wallet_topup', auth()->user()->name .' topped up wallet ', 'regular');

            systemNotification($user, 'success', 'Wallet Topup', '$'.$update->amount.' Wallet Topup Successful');

            return redirect('success');
        }else{
            return redirect('error');
        }
    }

    public function walletTop()
    {
        $url = request()->fullUrl();
        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);

        $ref = $params['trxref']; //paystack
        $res = verifyTransaction($ref); //
   
        $amount = $res['data']['amount'];

        $percent = 2.90/100 * $amount;
        $formatedAm = $percent;
        $newamount = $amount - $formatedAm; //verify transaction
        $creditAmount = $newamount / 100;
        
        $user = Auth::user();

       if($res['data']['status'] == 'success') //success - paystack
       {
            paymentUpdate($ref, 'successful'); //update transaction
            
            $wallet = Wallet::where('user_id', auth()->user()->id)->first();
            $wallet->balance += $creditAmount;
            $wallet->save();
            
            $name = auth()->user()->name;
            activityLog(auth()->user(), 'wallet_topup', $name .' topped up wallet ', 'regular');
            
            systemNotification($user, 'success', 'Wallet Topup', 'NGN'.$creditAmount.' Wallet Topup Successful');

            return back()->with('success', 'Wallet Topup Successful'); //redirect('success');
       }else{
        return redirect('error');
       }
    }

    public function flutterwaveWalletTopUp(){
       
        $url = request()->fullUrl();
        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);
        $status = $params['status'];
        if($status == 'cancelled'){
            return back()->with('error', 'Transaction terminated');
        }
        $tx_id = $params['transaction_id'];
        $ref = $params['tx_ref'];
        $res = flutterwaveVeryTransaction($tx_id);

        if($res['status'] == 'success'){
            $ver = paymentUpdate($ref, 'successful', $res['data']['amount_settled']);

            // $wallet = Wallet::where('user_id', auth()->user()->id)->first();
            // $wallet->balance += $res['data']['amount_settled'];//->amount;
            // $wallet->save();

            if($ver){
                $currency = auth()->user()->wallet->base_currency;

                 creditWallet(auth()->user(), $currency, $res['data']['amount_settled']);

                $name = auth()->user()->name;
                activityLog(auth()->user(), 'wallet_topup', $name .' topped up wallet ', 'regular');
                
                systemNotification(auth()->user(), 'success', 'Wallet Topup', 'NGN'.$ver->amount.' Wallet Topup Successful');

                return back()->with('success', 'Wallet Topup Successful'); 

            }
            
        }
    }

    public function storeWithdraw(Request $request)
    {
        $baseCurrency = auth()->user()->wallet->base_currency;
        if( $baseCurrency == 'NGN'){
          

            $request->validate([
                'amount' => 'required|numeric|min:2500',
            ], [
                'amount.min' => 'The amount must be at least 2500.',
            ]);
            

            if($request->balance >= 50000){
                return back()->with('error', 'This transaction is not allowed, contact customer care');
            }

            $check = PaymentTransaction::where('user_id', auth()->user()->id)
                    ->where('type', 'cash_withdrawal')
                    ->whereDate('created_at', Carbon::today())
                    ->get(['id', 'amount', 'type']);
            
            if(count($check) > 1){
                return back()->with('error', 'This transaction is not allowed count, contact customer care');
            }
            
            if($check->sum('amount') >= '50000'){
                return back()->with('error', 'This transaction is not allowed, contact customer care');
            }

            $user = User::where('id', auth()->user()->id)->first();

            $accountCreationDate = new Carbon($user->created_at);

            if($accountCreationDate->diffInDays(Carbon::now()) <= 10){
                return back()->with('error', 'You cannot make withdrawal at the moment');
            }

            $wallet = Wallet::where('user_id', auth()->user()->id)->first();

            if($wallet->balance < $request->balance)
            {
                return back()->with('error', 'Insufficient balance');
            }

            $bankInformation = BankInformation::where('user_id', auth()->user()->id)->first();
            if($bankInformation){
                $this->processWithdrawals($request, 'NGN', 'paystack', '');
                return back()->with('success', 'Withdrawal Successfully queued');
                //  $bankList = PaystackHelpers::bankList();
                //  return view('user.bank_information', ['bankList' => $bankList]);
            }else{
                return redirect('profile')->with('info', 'Please scroll down to Bank Account Details to update your information');
            }

        }else{

            $request->validate([
                'balance' => 'required',
            ]);

            $wallet = Wallet::where('user_id', auth()->user()->id)->first();
            if($baseCurrency == 'USD'){
                if($wallet->usd_balance < $request->balance)
                {
                    return back()->with('error', 'Insufficient balance');
                }
            }else{
                if($wallet->base_currency_balance < $request->balance)
                {
                    return back()->with('error', 'Insufficient balance');
                }
            }
            

            if($baseCurrency == 'GHS'){
                $accountBank = $request->account_bank;
            }else{
                $accountBank = 'MPS';
            }

            $payload = [
                 "account_bank" => $accountBank,
                  "account_number" => $request->account_number,
                  "amount" => $request->balance,
                  "currency" => $baseCurrency,
                  "beneficiary_name" => $request->beneficiary_name,
                  "narration" => 'Freebyz Withdrawal',
                  "meta" => [
                        "sender" => "Flutterwave Developers",
                        "sender_country" => "NG",
                        "mobile_number" => "23457558595"
                   ]
            ];
            
            // return flutterwaveTransfer($payload);

        //    return $this->processForeignWithdrawal($payload);
           $this->processWithdrawals($request, $baseCurrency, 'flutterwave', $payload);

            return back()->with('success', 'Withdrawal Successfully queued');

        }
    }

    public function processWithdrawals($request, $currency, $channel, $payload){

        $amount = $request->balance;
        $percent = 5/100 * $amount;
        $formatedAm = $percent;
        $newamount_to_be_withdrawn = $amount - $formatedAm;
 
        $ref = time();
        
        if(Carbon::now()->format('l') == 'Friday'){
            $nextFriday = Carbon::now()->endOfDay();
        }else{
            $nextFriday = Carbon::now()->next('Friday')->format('Y-m-d h:i:s');
        }

         $wallet = Wallet::where('user_id', auth()->user()->id)->first();
         if($currency == 'USD' || $currency == 'Dollar'){
            $wallet->usd_balance -= $request->balance;
            $wallet->save();
         }elseif($currency == 'NGN' || $currency == 'Naira'){
            $wallet->balance -= $request->balance;
            $wallet->save();
         }else{
            $wallet->base_currency_balance -= $request->balance;
            $wallet->save();
         }
        //  return $payload;
 
        $withdrawal = Withrawal::create([
             'user_id' => auth()->user()->id, 
             'amount' => $newamount_to_be_withdrawn,
             'next_payment_date' => $nextFriday,
             'paypal_email' => $currency == 'USD' ? $request->paypal_email : null,
             'is_usd' => $currency == 'USD' ? true : false,
             'base_currency' => $currency,
             'content' => $payload == '' ? null : $payload
         ]);

        //process dollar withdrawal
        PaymentTransaction::create([
            'user_id' => auth()->user()->id,
            'campaign_id' => '1',
            'reference' => $ref,
            'amount' => $newamount_to_be_withdrawn,
            'status' => 'successful',
            'currency' => $currency,
            'channel' => $channel,
            'type' => 'cash_withdrawal',
            'description' => 'Cash Withdrawal from '.auth()->user()->name,
            'tx_type' => 'Credit',
            'user_type' => 'regular'
        ]);

        //admin commission
            $adminWallet = Wallet::where('user_id', '1')->first();
            if($currency == 'USD' || $currency == 'Dollar'){
                $adminWallet->usd_balance += $formatedAm;
                $adminWallet->save();
             }elseif($currency == 'NGN' || $currency == 'Naira'){
                $adminWallet->balance += $formatedAm;
                $adminWallet->save();
             }else{
                $adminWallet->base_currency_balance += $formatedAm;
                $adminWallet->save();
             }
            //Admin Transaction Tablw
            PaymentTransaction::create([
                'user_id' => 1,
                'campaign_id' => '1',
                'reference' => $ref,
                'amount' => $percent,
                'status' => 'successful',
                'currency' => $currency,
                'channel' => $channel,
                'type' => 'withdrawal_commission',
                'description' => 'Withdrwal Commission from '.auth()->user()->name,
                'tx_type' => 'Credit',
                'user_type' => 'admin'
            ]);
            activityLog(auth()->user(), 'withdrawal_request', auth()->user()->name .'sent a withdrawal request of NGN'.number_format($amount), 'regular');
            // $bankInformation = BankInformation::where('user_id', auth()->user()->id)->first();
            $cur = $currency == 'USD' ? '$' : 'NGN';
            systemNotification(Auth::user(), 'success', 'Withdrawal Request', $cur.$request->balance.' was debited from your wallet');
        
            // $user = User::where('id', '1')->first();
            // $subject = 'Withdrawal Request Queued!!';
            // $content = 'A withdrwal request has been made and it being queued';
            // Mail::to('freebyzcom@gmail.com')->send(new GeneralMail($user, $content, $subject, ''));

            return $withdrawal;
    }

    public function switchWallet(Request $request){
        auth()->user()->wallet()->update(['base_currency' => $request->currency]);
        systemNotification(Auth::user(), 'success', 'Currency Switch', 'Currency switched to '.$request->currency);
        
        return back()->with('success', 'Currency switched successfully');
    }
}
