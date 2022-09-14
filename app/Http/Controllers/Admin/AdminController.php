<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PaystackHelpers;
use App\Http\Controllers\Controller;
use App\Mail\ApproveCampaign;
use App\Mail\MassMail;
use App\Mail\UpgradeUser;
use App\Models\Campaign;
use App\Models\CampaignWorker;
use App\Models\Games;
use App\Models\PaymentTransaction;
use App\Models\Question;
use App\Models\Referral;
use App\Models\Reward;
use App\Models\User;
use App\Models\UserScore;
use App\Models\Wallet;
use App\Models\Withrawal;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createGame()
    {
        $user = auth()->user();

        if($user->hasRole('admin'))
        {
            //$game = Games::find($id);
            return view('admin.create_game');
        }
    }

    public function createQuestion()
    {
        $questions = Question::all();
        return view('admin.add_question', ['question' => $questions]);
    }

    publiC function storeQuestion(Request $request)
    {   
        $question = Question::create($request->all());
        $question->save();

        $get_answer = DB::table('questions')->select($request->correct_answer)->latest()->first();
        $collect = collect($get_answer);
        $value = $collect->shift();
        $question->update(['correct_answer' => $value]);

        return back()->with('status', 'Question Created Successfully');
    }

    public function updateQuestion(Request $request)
    {
        $question = Question::where('id', $request->id)->first();
        $question->content = $request->content;
        $question->option_A = $request->option_A;
        $question->option_B = $request->option_B;
        $question->option_C = $request->option_C;
        $question->option_D = $request->option_D;
        $question->correct_answer = $request->correct_answer;
        $collect = collect($question);
        $value = $collect->shift();
        $question->correct_answer = $value;
        $question->save();

        return back()->with('status', 'Question Updated Successfully');
        
    }

    public function gameStatus($id)
    {

        $game = Games::where('id', $id)->first();

        if($game->status == '1'){
            $game->status = '0';
            $game->save();
        }else{
            $game->status = '1';
            $game->save();
        }

         return back()->with('status', 'Status Changed Successfully');

    }

    public function gameCreate()
    {
        $user = auth()->user();

        if($user->hasRole('admin'))
        {
            //$game = Games::find($id);
            return view('admin.create_game');
        }
    }

    public function gameStore(Request $request)
    {

        $slug = Str::slug($request->name);
        $game = Games::create([
            'name' => $request->name, 
            'type' => $request->type, 
            'number_of_winners' => $request->number_of_winners, 
            'slug' => $slug, 
            'time_allowed' => 0.25,//$request->time_allowed, 
            'number_of_questions'=>$request->number_of_questions,
            'status' => 1
        ]);
        // $game->save();

        return back()->with('status', 'Game Created Successfully');

    }

    public function updateAmount(Request $request)
    {
        $reward = Reward::where('id', $request->id)->first();
        $reward->name = $request->name;
        $reward->amount = $request->amount;
        $reward->save();
        return back()->with('status', 'Amount updated Successfully');

    }

    public function viewAmount()
    {
        $reward = Reward::all();
        return view('admin.update_amount', ['rewards' => $reward]);
    }

    public function listQuestion()
    {
        $questions = Question::orderBy('created_at', 'desc')->paginate('200');
        $question_count = Question::all()->count();
        return view('admin.question_list', ['questions' => $questions, 'question_count' => $question_count]);
    }

    public function viewActivities($id)
    {
        $game = Games::where('id', $id)->first();
        $activities = UserScore::where('game_id', $id)->orderBy('score', 'desc')->get();

        return view('admin.game_activities', ['activities' => $activities, 'game' => $game]);
    }

    public function assignReward(Request $request)
    {
        
        if(empty($request->id))
        {
             return back()->with('error', 'Please Select A Score');
        }
        $reward = Reward::where('name', $request->name)->first()->amount;
        $formattedReward = number_format($reward,2);
        foreach($request->id as $id)
        {
            $score = UserScore::where('id', $id)->first();
            $score->reward_type = $request->name;
            $score->save();

            $phone = '234'.substr($score->user->phone, 1);
            $message = "Hello ".$score->user->name. " you have a ".$request->name." reward of ".$formattedReward." from Freebyz.com. Please login to cliam it. Thanks";
            PaystackHelpers::sendNotificaion($phone, $message);
        }

        return back()->with('status', 'Reward Assigned Successfully');

    }

    public function sendAirtime()
    {
        return PaystackHelpers::reloadlyAuth0Token();
    }

    public function userList()
    {
        $users = User::where('role', 'regular')->orderBy('created_at', 'desc')->get();
        return view('admin.users', ['users' => $users]);
    }
    public function verifiedUserList()
    {
        $verifiedUsers = User::where('role', 'regular')->where('is_verified', '1')->orderBy('created_at', 'desc')->get();
        return view('admin.verified_user', ['verifiedUsers' => $verifiedUsers]);
    }

    public function adminTransaction()
    {
        $list = PaymentTransaction::where('user_type', 'admin')->where('status', 'successful')->orderBy('created_at', 'DESC')->get();
        return view('admin.admin_transactions', ['lists' => $list]);
    }
    public function userTransaction()
    {
        $list = PaymentTransaction::where('user_type', 'regular')->where('status', 'successful')->orderBy('created_at', 'DESC')->get();
        return view('admin.user_transactions', ['lists' => $list]);
    }

    public function userInfo($id)
    {
        $info = User::where('id', $id)->first();
        return view('admin.user_info', ['info' => $info]);
    }

    public function withdrawalRequest()
    {
        $withdrawal = Withrawal::orderBy('created_at', 'DESC')->get();
        return view('admin.withdrawal', ['withdrawals' => $withdrawal]);
    }

    public function upgradeUser($id)
    {
        $getUser = User::where('id', $id)->first();
        $getUser->is_verified = true;
        $getUser->save();

         //credit User with 1,000 bonus
         $bonus = Wallet::where('user_id',$getUser->id)->first();
         $bonus->bonus += '1000';
         $bonus->save();

        $ref = time();
        PaymentTransaction::create([
            'user_id' => $getUser->id,
            'campaign_id' => '1',
            'reference' => $ref,
            'amount' => 500,
            'status' => 'successful',
            'currency' => 'NGN',
            'channel' => 'paystack',
            'type' => 'upgrade_payment',
            'description' => 'Manual Ugrade Payment'
        ]);

        if($bonus){
            //user transction table for bonus 
           PaymentTransaction::create([
               'user_id' => $getUser->id,
               'campaign_id' => '1',
               'reference' => time(),
               'amount' => 1000,
               'status' => 'successful',
               'currency' => 'NGN',
               'channel' => 'paystack',
               'type' => 'upgrade_bonus',
               'description' => 'Verification Bonus for '.$getUser->name,
               'tx_type' => 'Credit',
               'user_type' => 'regular'
           ]);
       }


        $referee = \DB::table('referral')->where('user_id',  $getUser->id)->first();
          
       if($referee){
        $wallet = Wallet::where('user_id', $referee->referee_id)->first();
        $wallet->balance += 250;
        $wallet->save();

        $refereeUpdate = Referral::where('user_id', $getUser->id)->first(); //\DB::table('referral')->where('user_id',  auth()->user()->id)->update(['is_paid', '1']);
        $refereeUpdate->is_paid = true;
        $refereeUpdate->save();

        $referee_user = User::where('id', $referee->referee_id)->first();
        ///Transactions
        PaymentTransaction::create([
            'user_id' => $referee_user->id,///auth()->user()->id,
            'campaign_id' => '1',
            'reference' => $ref,
            'amount' => 250,
            'status' => 'successful',
            'currency' => 'NGN',
            'channel' => 'paystack',
            'type' => 'referer_bonus',
            'description' => 'Referer Bonus from '.auth()->user()->name
        ]);

        $adminWallet = Wallet::where('user_id', '1')->first();
        $adminWallet->balance += 250;
        $adminWallet->save();
        //Admin Transaction Tablw
        PaymentTransaction::create([
            'user_id' => 1,
            'campaign_id' => '1',
            'reference' => $ref,
            'amount' => 250,
            'status' => 'successful',
            'currency' => 'NGN',
            'channel' => 'paystack',
            'type' => 'referer_bonus',
            'description' => 'Referer Bonus from '.$getUser->name,
            'tx_type' => 'Credit',
            'user_type' => 'admin'
        ]);

       }else{

        $adminWallet = Wallet::where('user_id', '1')->first();
        $adminWallet->balance += 500;
        $adminWallet->save();
         //Admin Transaction Tablw
         PaymentTransaction::create([
            'user_id' => 1,
            'campaign_id' => '1',
            'reference' => $ref,
            'amount' => 500,
            'status' => 'successful',
            'currency' => 'NGN',
            'channel' => 'paystack',
            'type' => 'direct_referer_bonus',
            'description' => 'Direct Referer Bonus from '.$getUser->name,
            'tx_type' => 'Credit',
            'user_type' => 'admin'
        ]);
       
       }

       //Mail::send($getUser->email)
       Mail::to($getUser->email)->send(new UpgradeUser($getUser));
       return back()->with('success', 'Upgrade Successful');
    }

    public function campaignList()
    {
        $campaigns = Campaign::orderBy('created_at', 'ASC')->get();
        return view('admin.campaign_list', ['campaigns' => $campaigns]);
    }

    public function unapprovedJobs()
    {
        $list = CampaignWorker::where('status', 'Pending')->orderBy('created_at', 'DESC')->get();
        return view('admin.unapproved_list', ['campaigns' => $list]); 
    }

    public function massApproval(Request $request){
       $ids = $request->id;
       if(empty($ids)){
        return back()->with('error', 'Please select at least one item');
       }

       foreach($ids as $id){
        $ca = CampaignWorker::where('id', $id)->first();
        $ca->status = 'Approved';
        $ca->save();
        
        $wallet = Wallet::where('user_id', $ca->user_id)->first();
        $wallet->balance += $ca->amount;
        $wallet->save();
        $ref = time();

        PaymentTransaction::create([
            'user_id' => $ca->user_id,
            'campaign_id' => '1',
            'reference' => $ref,
            'amount' => $ca->amount,
            'status' => 'successful',
            'currency' => 'NGN',
            'channel' => 'paystack',
            'type' => 'campaign_payment',
            'description' => 'Campaign Payment for '.$ca->campaign->post_title,
            'tx_type' => 'Credit',
            'user_type' => 'regular'
        ]);

       $subject = 'Job Approved';
       $status = 'Approved';
       Mail::to($ca->user->email)->send(new ApproveCampaign($ca, $subject, $status));

       }
       return back()->with('success', 'Mass Approval Successful');

    }

    public function massMail()
    {
        return view('admin.mass_mail');
    }

    public function sendMassMail(Request $request){
        if($request->type == 'all'){
            $users = User::where('is_verified', 0)->where('role', 'regular')->get();
        }else{
            $users = User::where('is_verified', 1)->where('role', 'regular')->get();
        }

        $message = $request->message;
        $subject = $request->subject;
        foreach($users as $user){
            Mail::to($user->email)->send(new MassMail($user, $message, $subject));
        }
        return back()->with('success', 'Mail Sent Successful');
    }

    


   
}
