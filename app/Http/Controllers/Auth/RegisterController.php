<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\PaystackHelpers;
use App\Helpers\Sendmonny;
use App\Helpers\SystemActivities;
use App\Http\Controllers\Controller;
use App\Models\AccountInformation;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function registerUser(Request $request){
       
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'source' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

       $payload = [
            'first_name' =>  $request->first_name,
            'last_name' =>  $request->last_name,
            'password' => $request->password,
            'password_confirmation' => $request->password,
            'email' => $request->email,
            'username' => Str::random(7),
            'phone_number' => '234'.substr($request->phone, 1), //substr($request->phone_number['full'], 1),
            'user_type' =>"CUSTOMER",
            'mobile_token' => Str::random(7),
            'source' => 'Freebyz'
        ];

        if(walletHandler() == 'local'){
            $user = $this->createUser($request); //CREATE USER ON FREEBYZ
        }else{
            $user = $this->createUser($request); //CREATE USER ON FREEBYZ
            if($user){
                $location = PaystackHelpers::getLocation(); //get user location dynamically
                if($location->countryName == 'United States'){
                    $sendMonnyApi = $this->sendMonny($payload);
                    if($sendMonnyApi['status'] == true){
                    $this->processAccountInformation($sendMonnyApi,$user);
                    }
                }else{
                    AccountInformation::create([
                        'user_id' => $user->id,
                        'wallet_id' => '1234567890'
                    ]);
                }
            }
        }
        
        
        Auth::login($user);
        PaystackHelpers::userLocation('Registeration');
        return redirect('/home');
    }

    public function createUser($request){
        $ref_id = $request->ref_id;
        $name = $request->first_name.' '.$request->last_name;
        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'country' => $request->country,
            'phone' => $request->phone,
            'source' => $request->source,
            'password' => Hash::make($request->password),
        ]);
        $user->referral_code = Str::random(7);
        $user->save();
        Wallet::create(['user_id'=> $user->id, 'balance' => '0.00']);
        if($ref_id != 'null'){
            \DB::table('referral')->insert(['user_id' => $user->id, 'referee_id' => $ref_id]);
        }
        return $user;
    }

    public function sendMonny($payload){
        return Sendmonny::sendUserToSendmonny($payload);
    }

    public function processAccountInformation($sendMonnyApi, $user){
        AccountInformation::create([
            'user_id' => $user->id,
            '_user_id' => $sendMonnyApi['data']['user']['user_id'],
            'wallet_id' => $sendMonnyApi['data']['wallet']['id'],
            'account_name' => $sendMonnyApi['data']['wallet']['account_name'],
            'account_number' => $sendMonnyApi['data']['wallet']['account_number'],
            'bank_name' => $sendMonnyApi['data']['wallet']['bank'],
            'bank_code' => $sendMonnyApi['data']['wallet']['bank_code'],
            'provider' => 'Sendmonny - Sudo',
            'currency' => $sendMonnyApi['data']['wallet']['currency'],
        ]);

        $activate = User::where('id', $user->id)->first();
        $activate->is_wallet_transfered = true;
        $activate->save();
    }


    public function loginUser(Request $request){
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        // if($user->is_blacklisted){
        //     return view('blocked');
        // }
        if(Hash::check(trim($request->password), $user->password)){
            if($user->role != 'admin'){
                $location = PaystackHelpers::getLocation(); //get user specific location
                if($location == "United States"){ //check if the person is in Nigeria
                    if($user->is_wallet_transfered == false){
                        //activate sendmonny wallet and fund wallet
                        if(walletHandler() == 'sendmonny'){ 
                            if($user->is_wallet_transfered == false){
                                activateSendmonnyWallet($user, $request->password); //hand sendmonny 
                            }
                        }
                    }
                }
            }
            // PaystackHelpers::userLocation('Login');
            SystemActivities::loginPoints($user);

            Auth::login($user); //log user in
            return redirect('home'); //redirect to home
        }else{
            return back()->with('error', 'Email or Password is incorrect');
        }
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            // 'phone_number' => ['required', 'numeric'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $ref_id = $data['ref_id'];
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'country' => $data['country'],
            'phone' => $data['phone_number']['full'],
            'source' => $data['source'],
            'password' => Hash::make($data['password']),
        ]);
        $user->referral_code = Str::random(7);
        $user->save();
        Wallet::create(['user_id'=> $user->id, 'balance' => '0.00']);
        if($ref_id != 'null'){
            \DB::table('referral')->insert(['user_id' => $user->id, 'referee_id' => $ref_id]);
        }
       
        return $user;
    }

    public function referral_register($referral_code)
    {
        $name = User::where('referral_code', $referral_code)->first();
        if(!$name){
            return view('auth.error', ['error' => 'Invalid referral code']);
        }
        return view('auth.ref_register', ['name' => $name]);
    }

    
}
