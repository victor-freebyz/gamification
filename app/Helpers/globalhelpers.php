<?php

use App\Helpers\PaystackHelpers;
use App\Helpers\Sendmonny;
use App\Helpers\SystemActivities;
use App\Models\AccountInformation;
use App\Models\Category;
use App\Models\ConversionRate;
use App\Models\Notification;
use App\Models\Profile;
use App\Models\Settings;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

if (!function_exists('GetSendmonnyUserId')) {  //sendmonny user idauthenticated user
    function GetSendmonnyUserId()
    {
       return AccountInformation::where('user_id', auth()->user()->id)->first()->_user_id;
    }
}
if (!function_exists('GetSendmonnyUserWalletId')) { //sendmonny wallet id of authenticated user
    function GetSendmonnyUserWalletId()
    {
       return AccountInformation::where('user_id', auth()->user()->id)->first()->wallet_id;
    }
}

if(!function_exists('adminCollection')){
    function adminCollection(){
        $walletId = env('COLLECTIION_WALLET_ID');
        $userId = env('COLLECTIION_USER_ID');

        $data['wallet_id'] = $walletId;
        $data['user_id'] = $userId;

        return $data;
    }
}

if(!function_exists('adminRevenue')){
    function adminRevenue(){
        $walletId = env('REVENUE_WALLET_ID');
        $userId = env('REVENUE_USER_ID');

        $data['wallet_id'] = $walletId;
        $data['user_id'] = $userId;

        return $data;
    }
}

if(!function_exists('accessToken')){
    function accessToken(){
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(env('SENDMONNY_URL').'authenticate', [
            "phone_number"=>env('PHONE'),
	        "password"=>env('PASS')
        ]);
        return json_decode($res->getBody()->getContents(), true)['data']['token'];
    }
}

if(!function_exists('userWalletId')){
    function userWalletId($id){
        return AccountInformation::where('user_id', $id)->first()->wallet_id;
    }
}

if(!function_exists('isBlacklisted')){
    function isBlacklisted($user){
        $blackist = User::where('id', $user->id)->is_blacklisted;
        if($blackist == true){
            return true;
        }else{
            return false;
        }
    }
}

if(!function_exists('walletHandler')){
    function walletHandler(){
        return Settings::where('status', true)->first()->name;
    }
}

if(!function_exists('setWalletBaseCurrency')){
    function setWalletBaseCurrency(){
        $wall = Wallet::where('user_id', auth()->user()->id)->first();
        // if(!$wall){
        //     Wallet::create(['user_id' => auth()->user()->id]);
        // }
        if($wall->base_currency == null){
            $location = PaystackHelpers::getLocation();
            $wall->base_currency = $location == "Nigeria" ? 'Naira' : 'Dollar';
            $wall->save();
        }
       return $wall;
    }
}

if(!function_exists('setProfile')){
    function setProfile($user){
        $profile = Profile::where('user_id', $user->id)->first();
        if(!$profile){
           $profile =  Profile::create(['user_id' => $user->id]);
        }
       return $profile;
    }
}

if(!function_exists('activateSendmonnyWallet')){
    function activateSendmonnyWallet($user, $password){
       
        $initials = SystemActivities::getInitials($user->phone);
            $phone = '';
            if($initials == 0){
                $phone = '234'.substr($user->phone, 1);
            }elseif($initials == '+'){
                $phone = substr($user->phone, 1);
            }elseif($initials == 2){
                $phone = $user->phone;
            }
        $name = explode(" ", $user->name);

        $payload = [
            'first_name' => $name[0],
            'last_name' => (isset($name[1]) ? $name[1] : 'sendmonny'),
            'password' => $password,
            'password_confirmation' => $password,
            'email' => $user->email,
            'username' => Str::random(7),
            'phone_number' => $phone, //'234'.substr(auth()->user()->phone, 1), //substr($request->phone_number['full'], 1),
            'user_type' => "CUSTOMER",
            'mobile_token' => Str::random(7),
            'source' => 'Freebyz'
        ];

       $sendMonny = Sendmonny::sendUserToSendmonny($payload);
       if($sendMonny['status'] == true){
            $account = AccountInformation::create([
                'user_id' => $user->id,
                '_user_id' => $sendMonny['data']['user']['user_id'],
                'wallet_id' => $sendMonny['data']['wallet']['id'],
                'account_name' => $sendMonny['data']['wallet']['account_name'],
                'account_number' => $sendMonny['data']['wallet']['account_number'],
                'bank_name' => $sendMonny['data']['wallet']['bank'],
                'bank_code' => $sendMonny['data']['wallet']['bank_code'],
                'provider' => 'Sendmonny - Sudo',
                'currency' => $sendMonny['data']['wallet']['currency'],
            ]);

            $activate = User::where('id', $user->id)->first();
            $activate->is_wallet_transfered = true;
            $activate->save();

            $wallet = Wallet::where('user_id', $user->id)->first();
             $payload = [
                "sender_wallet_id" => adminCollection()['wallet_id'], //freebyz admin wallet id
                "sender_user_id" => adminCollection()['user_id'], //freebyzadmin sendmonny userid
                "amount" => $wallet->balance,
                "pin"=> "2222",
                "narration" => "Sendmonny Wallet Transfer",
                "islocal" => true,
                "reciever_wallet_id" => userWalletId($user->id)
            ];
        
            $completeTransfer = Sendmonny::transfer($payload, accessToken());
            if($completeTransfer['status'] == true){
                $wallet->balance = 0;
                $wallet->save();
            }
        }
       return $completeTransfer;
    }  
}

if(!function_exists('conversionRate')){
    function conversionRate(){
        return ConversionRate::where('status', true)->first()->rate; //Settings::where('status', true)->first()->name;
    }
}

if(!function_exists('paypalPayment')){
    function paypalPayment($amount, $url){

        $res = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->withBasicAuth(env('PAYPAL_CLIENT_ID'), env('PAYPAL_CLIENT_SECRET'))
        ->post(env('PAYPAL_URL').'checkout/orders', [
            "intent"=> "CAPTURE",
            "purchase_units"=> [
                [
                    // "items"=> [
                    //     [
                    //         "name"=> $name,
                    //         "description"=> $description,
                    //         "quantity"=> "1",
                    //         "unit_amount"=> [
                    //             "currency_code"=> "USD",
                    //             "value"=> $amount
                    //         ]
                    //     ]
                    // ],
                    "reference_id"=> time(),
                    "amount"=> [
                        "currency_code"=> "USD",
                        "value"=> $amount,
                        "breakdown"=> [
                            "item_total"=> [
                                "currency_code"=> "USD",
                                "value"=> $amount
                            ]
                        ]
                    ]
                ]
            ],
            "application_context"=> [
                "return_url"=> url($url),
                "cancel_url"=> url('/home')
            ]
        ]);
        return json_decode($res->getBody()->getContents(), true);
    }
}

if(!function_exists('capturePaypalPayment')){
    function capturePaypalPayment($id){

        $url = env('PAYPAL_URL').'checkout/orders/'.$id.'/capture';

        // Request payload
        $data = [];

        // Basic Authorization credentials
        $client_id = env('PAYPAL_CLIENT_ID');
        $client_secret = env('PAYPAL_CLIENT_SECRET');

        // Initialize cURL
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret),
        ]);

        // Execute the request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            // Handle the error
        } else {
            return json_decode($response, true); //return response()->json([$response], 201);
        }
        // Close cURL resource
        curl_close($ch);
    }
}

if(!function_exists('systemNotification')){
    function systemNotification($user, $category, $title, $message){
        
        $notification = Notification::create([
            'user_id' => $user->id,
            'category' => $category,
            'title' => $title,
            'message'=> $message
        ]);

        return $notification;
    }
}

if(!function_exists('checkWalletBalance')){
    function checkWalletBalance($user, $type, $amount){
        
       if($type == 'Naira'){
        $wallet =  Wallet::where('user_id', $user->id)->first();
            if((int) $wallet->balance >= $amount){
                return true;
            }else{
                return false;
            }
       }elseif($type == 'Dollar'){
        
        $wallet =  Wallet::where('user_id', $user->id)->first();
        
            if((int) $wallet->usd_balance >= $amount){
                return true;
            }else{
                return false;
            }

       }else{
        return 'invalid';
       }
       
    }
}

if(!function_exists('creditWallet')){
    function creditWallet($user, $type, $amount){
        
       if($type == 'Naira'){
            $wallet =  Wallet::where('user_id', $user->id)->first();
            $wallet->balance += $amount;
            $wallet->save();
            return $wallet;
       }elseif($type == 'Dollar'){
            $wallet =  Wallet::where('user_id', $user->id)->first();
            $wallet->usd_balance += $amount;
            $wallet->save();
            return $wallet;

       }else{
        return 'invalid';
       }
       
    }
}

if(!function_exists('debitWallet')){
    function debitWallet($user, $type, $amount){
        
       if($type == 'Naira'){
            $wallet =  Wallet::where('user_id', $user->id)->first();
            $wallet->balance -= $amount;
            $wallet->save();
            return $wallet;
       }elseif($type == 'Dollar'){
            $wallet =  Wallet::where('user_id', $user->id)->first();
            $wallet->usd_balance -= $amount;
            $wallet->save();
            return $wallet;

       }else{
        return 'invalid';
       }
       
    }
}

if(!function_exists('dollar_naira')){
    function dollar_naira(){
       return ConversionRate::where('from', 'Dollar')->first()->amount;
    }
}


if(!function_exists('naira_dollar')){
    function naira_dollar(){
        return ConversionRate::where('from', 'Naira')->first()->amount;
    }
}

if(!function_exists('short_name')){
    function short_name($name){
        $name = explode(" ", $name);
        return $name['0'];
    }
}

if(!function_exists('flutterwaveVirtualAccount')){
    function flutterwaveVirtualAccount($payload){

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('FL_SECRET_KEY')
        ])->post('https://api.flutterwave.com/v3/virtual-account-numbers', $payload)->throw();

        return json_decode($res->getBody()->getContents(), true);
        
    }
}

if(!function_exists('flutterwavePaymentInitiation')){
    function flutterwavePaymentInitiation($payload){

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('FL_SECRET_KEY')
        ])->post('https://api.flutterwave.com/v3/payments', $payload)->throw();

        return json_decode($res->getBody()->getContents(), true);
        
    }
}

if(!function_exists('flutterwaveVeryTransaction')){
    function flutterwaveVeryTransaction($id){

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('FL_SECRET_KEY')
        ])->get('https://api.flutterwave.com/v3/transactions/'.$id.'/verify')->throw();

        return json_decode($res->getBody()->getContents(), true);
        
    }
}

if(!function_exists('sendGridEmails')){
    function sendGridEmails($payload){
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('SENDGRID_API_KEY')
        ])->post('https://api.sendgrid.com/v3/mail/send', $payload)->throw();

    }
}

