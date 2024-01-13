<?php

namespace App\Http\Controllers;

use App\Helpers\Analytics;
use App\Helpers\CapitalSage;
use App\Helpers\PaystackHelpers;
use App\Models\Answer;
use App\Models\Campaign;
use App\Models\CampaignWorker;
use App\Models\Games;
use App\Models\PartnerSubscription;
use App\Models\PaymentTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserScore;
use Illuminate\Http\Request;

class GeneralController extends Controller
{

    public function fix(){

        //  $users = User::where('role', 'regular')->where('country', 'Nigeria')->get('phone');
        //  $list = [];
        //  foreach($users as $user){
        //     $formattedNumber = '';
        //     $number = $user;//strlen(strval($user));
            
        //     $list[] =$number;
        //  }
        //  return $list;

        
        $campaigns = Campaign::where('status', 'Live')->orderBy('created_at', 'DESC')->get();
        $list = [];
        foreach($campaigns as $key => $value){
            $data['pending'] = 'Pending';
            $data['approve'] = 'Approved';
            $lisCamp = Campaign::where('id', $value->id)->first();
            $lisCamp->pending_count = $value->completed->count();
            $lisCamp->completed_count = $value->completed()->where('status', '=', 'Approved')->count();
            $lisCamp->save();

            // setIsComplete($value->id);
            setPendingCount($value->id);
            // if($completed >= $value->number_of_staff){
            //     Campaign::where('id', $value->id)->update(['is_completed' => true]);
            // }
        }

        return 'okay';
    }

    public function promo(){
        return view('promo');
    }


    public function landingPage()
    {  
        Analytics::dailyVisit();
        $users = User::where('role', 'regular')->count();
        $workers = CampaignWorker::all()->count();
        $transactions = PaymentTransaction::inRandomOrder()->limit(10)->where('type', 'cash_withdrawal')->select(['user_id','amount','description'])->get();
        return view('landingPage', ['transactions' => $transactions, 'users' => $users, 'workers' => $workers ]);// ['prizesWon' => $prizesWon, 'gameplayed' => $gameplayed, 'user' => $user]);
    }

    public function contact()
    {
        Analytics::dailyVisit();
        return view('contact');
    }

    public function goal()
    {
        Analytics::dailyVisit();
        return view('goal');
    }

    public function winnerlist()
    {
        $winners = UserScore::where('reward_type', '!=', '')->orderBy('created_at', 'desc')->get();
        return view('winner', ['winners' => $winners]);
    }

    public function gamelist()
    {
        Analytics::dailyVisit();
        $games = Games::orderBy('created_at', 'desc')->get();
        return view('gamelist', ['games' => $games]);
    }

    public function terms()
    {
        Analytics::dailyVisit();
        return view('terms');
    }

    public function privacy()
    {
        Analytics::dailyVisit();
        return view('privacy');
    }
    
    public function make_money()
    {
        Analytics::dailyVisit();
        return view('make_money');
    }

    public function trackRecord()
    {
        Analytics::dailyVisit();
        return view('track_record');
    }

    public function faq()
    {
        Analytics::dailyVisit();
        return view('faq');
    }
    public function about()
    {
        Analytics::dailyVisit();
        return view('about');
    }

    public function download()
    {
        Analytics::dailyVisit();
        return view('download');
    }

    public function download_url(Request $request)
    {
        return $request;
    }

    public function wellahealth($ref){
        // return $ref;
        $user = User::where('referral_code', $ref)->first();
        if(!$user){
            return abort(404);
        }
        $display = [];
       foreach(listWellaHealthScriptions() as $list){
            $mysubscriptions = PartnerSubscription::where('user_id', $user->id)->first();//pluck('plan_code')->toArray();
            $display[] = [
                'data'=> $list, 
                'is_subscribed' => $list['planCode'] == @$mysubscriptions->plan_code ? true : false, 
                'subscriptionCode' => $list['planCode'] == @$mysubscriptions->plan_code ? @$mysubscriptions->subscription_code : null, 
            ];
        }
        return view('user.partner.wellahealth.external', ['subscriptions' => $display, 'ref' => $ref]);
        
        
    }

    public function processWellaHealth($ref, $planCode, $numberOfPersons, $amount, $type){
        $referral = User::where('referral_code', $ref)->first();
        return view('user.partner.wellahealth.process', [
            'ref' => $ref,
            'planCode' => $planCode,
            'numberOfPersons' => $numberOfPersons,
            'amount' => $amount,
            'type' => $type,
            'referral' => $referral
        ]);
        //return [$ref, $planCode, $numberOfPersons, $amount];
    }

    public function storeWellaHealth(Request $request){
        return $request;
    }
}
