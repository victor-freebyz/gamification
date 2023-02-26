<?php

namespace App\Http\Controllers;

use App\Helpers\PaystackHelpers;
use App\Models\Answer;
use App\Models\BankInformation;
use App\Models\Games;
use App\Models\Question;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserScore;
use Illuminate\Http\Request;
use AfricasTalking\SDK\AfricasTalking;
use AfricasTalking\SDK\Airtime;
use App\Models\Campaign;
use App\Models\CampaignWorker;
use App\Models\PaymentTransaction;
use App\Models\Referral;
use App\Models\Reward;
use App\Models\Wallet;
use Carbon\Carbon;
use Nette\Utils\Random;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        
        if($user->hasRole('admin')){
            // return 'admin';
            return redirect()->route('admin.home');
        }
       
        return redirect()->route('user.home');
    }

    public function userHome()
    {

        $user = User::where('id', auth()->user()->id)->first();
        if($user->phone == ''){
            return view('phone');
        }
        if($user->is_verified == true){
            $available_jobs = Campaign::where('status', 'Live')->orderBy('created_at', 'desc')->get();
        }else{
            $available_jobs = Campaign::where('status', 'Live')->where('campaign_amount', '<=', 10)->orderBy('created_at', 'desc')->get();
        }

        // $available_jobs = Campaign::where('status', 'Live')->orderBy('created_at', 'desc')->get();

        $completed = CampaignWorker::where('user_id', auth()->user()->id)->where('status', 'Approved')->count();
        return view('user.home', ['available_jobs' => $available_jobs, 'completed' => $completed]);
    }

    public function adminHome()
    {
        $campaigns = Campaign::where('status', 'Live')->get();
        $campaignWorker = CampaignWorker::all();
        $user = User::where('role', 'regular')->get();
        $wallet = Wallet::all();
        $ref_rev = Referral::where('is_paid', true)->count();
        $transactions = PaymentTransaction::where('user_type', 'admin')->get();
        $Wal = Wallet::where('user_id', auth()->user()->id)->first();
        
        $data = User::select(\DB::raw('DATE(created_at) as date'), \DB::raw('count(*) as total_reg'), \DB::raw('SUM(is_verified) as verified'))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();
        // DATE_FORMAT(created_at, "%d-%b-%Y")
        $result[] = ['Year','Registered','Verified'];
        foreach ($data as $key => $value) {
            $result[++$key] = [$value->date, (int)$value->total_reg, (int)$value->verified];
        }



    //    return $monthlyCounts = \DB::table('users')
    //                     ->selectRaw('DATE_FORMAT(created_at, "%b %Y") as month')
    //                     ->selectRaw('count(*) as count')
    //                     ->selectRaw('SUM(is_verified) as verified')
    //                     ->groupBy('month')
    //                     ->orderBy('month', 'asc')
    //                     ->get();

    //     $listResult[] = ['Month', 'Registered', 'Verified'];
    //     foreach ($monthlyCounts as $key => $value) {
    //         $listResult[++$key] = [(int)$value->month, (int)$value->count, (int)$value->verified];
    //     }

        return view('admin.index', [ 'users' => $user, 'campaigns' => $campaigns, 'workers' => $campaignWorker, 'wallet' => $wallet, 'ref_rev' => $ref_rev, 'tx' => $transactions, 'wal'=>$Wal])
        ->with('visitor',json_encode($result));//->with('monthly',json_encode($listResult));

    }

   
 
    public function savePhoneInformation(Request $request)
    {
        $this->validate($request, [
            'phone' => 'numeric|required|digits:11|unique:users'
        ]);

        $user = User::where('id', auth()->user()->id)->first();
        $user->phone = $request->phone;
        $user->source = $request->source;
        $user->save();
        return redirect('/home');
    }

    public function instruction()
    {
        $games = Games::where('status', '1')->first();
        return view('instruction', ['games' => $games]);
    }

    public function takeQuiz()
    {
        $games = Games::where('status', '1')->first();

        $userScore = UserScore::where('user_id', auth()->user()->id)->where('game_id', $games->id)->get();

        if(count($userScore) > 0)
        {
            return view('error');
        }
        $questions = Question::inRandomOrder()->limit(1)->first();
        return view('play', ['question' => $questions, 'game' => $games]);
    }

    public function storeAnswer(Request $request)
    {
        $question = Question::where('id', $request->question_id)->first();

        //getcorrect answer
        $correctAnswer = $question->correct_answer;
        $userAnswer = $request->option;

        if($userAnswer == $correctAnswer)
        {
            $isCorrect = 1;
        }else{
            $isCorrect = 0;
        }

        Answer::create([
            'game_id' => $request->game_id,
            'question_id' => $request->question_id,
            'user_id' => auth()->user()->id,
            'selected_option' => $request->option,
            'correct_option' => $question->correct_answer,
            'is_correct' => $isCorrect
        ]);

        return redirect('next/question');

    }

    public function nextQuestion()
    {
        $games = Games::where('status', '1')->first();

        $userScore = UserScore::where('user_id', auth()->user()->id)->where('game_id', $games->id)->get();

        if(count($userScore) > 0)
        {
            return view('error');
        }

        $questions = Question::inRandomOrder()->limit(1)->first();

        $answered = Answer::where('user_id', auth()->user()->id)->where('game_id', $games->id)->count();
        $index = $answered + 1;
        if($answered == $games->number_of_questions)
        {
            return redirect('submit/answers');
        }

        return view('next', ['question' => $questions, 'game' => $games, 'index' => $index]);
    }

    public function submitAnswers()
    {
        $games = Games::where('status', '1')->first();

        $getCorrectAnswers = Answer::where('game_id', $games->id)->where('user_id', auth()->user()->id)->where('is_correct', '1')->count();
        $percentage = ($getCorrectAnswers / $games->number_of_questions) * 100;

        $userScore = UserScore::where('user_id', auth()->user()->id)->where('game_id', $games->id)->get();

        if(count($userScore) > 0)
        {
            return view('completed', ['score' => $percentage]);

        }

         UserScore::Create(['user_id' => auth()->user()->id, 'game_id' => $games->id, 'score' => $percentage]);

        return view('completed', ['score' => $percentage]);
    }

    public function scores()
    {
        $scores = UserScore::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        return view('scores', ['scores' => $scores]);
    }

    public function redeemReward($id)
    {
        $reward_type = UserScore::where('id', $id)->first();
        if($reward_type->reward_type == 'CASH' && $reward_type->is_redeem == '0')
        {
            $bankInformation = BankInformation::where('user_id', auth()->user()->id)->first();
            if($bankInformation == null){
                $bankList = PaystackHelpers::bankList();
                return view('bank_information', ['bankList' => $bankList, 'id' => $id]);
            }

                $parameters = Reward::where('name', 'CASH')->first();
                $amount = $parameters->amount * 100;
                //transfer the fund
                $transfer = $this->transferFund($amount, $bankInformation->recipient_code);


               if($transfer['status'] == 'false'){
                if($transfer['data']['status'] == 'success' || $transfer['data']['status'] == 'pending')
                {
                    $userScore = UserScore::where('id', $id)->first();
                    $userScore->is_redeem = true;
                    $userScore->save();

                    Transaction::create([
                        'user_id' => auth()->user()->id,
                        'game_id' => $userScore->game_id,
                        'amount' => $transfer['data']['amount'],
                        'reward_type' => 'CASH',
                        'reference' => $transfer['data']['reference'],
                        'transfer_code' => $transfer['data']['transfer_code'],
                        'recipient' => $transfer['data']['recipient'],
                        'status' => $transfer['data']['status'],
                        'currency' => $transfer['data']['currency']
                    ]);
                    return redirect('score/list')->with('status', 'Money successfully sent to your account');
                }else{
                    return redirect('score/list')->with('error', 'There was an error while sending cash, please try again later');
                }

            }else{
                return redirect('score/list')->with('error', 'There was an error while sending cash, please try again later!!!');
            }
        }elseif($reward_type->reward_type == 'AIRTIME' && $reward_type->is_redeem == '0')
        {
            
            $parameters = Reward::where('name', 'AIRTIME')->first();
            //$phone = '+234'.substr(auth()->user()->phone, 1);
            $amount = $parameters->amount;
            $phone = auth()->user()->phone;

            
            return $airtime = $this->sendAirtime($phone, $amount);//['data'];              
            // if($airtime->errorMessage == "None")
            // {

            //     $userScore = UserScore::where('id', $id)->first();
            //         $userScore->is_redeem = true;
            //         $userScore->save();

            //         Transaction::create([
            //             'user_id' => auth()->user()->id,
            //             'game_id' => $userScore->game_id,
            //             'amount' =>  $airtime->totalAmount,//$transfer['data']['amount'],
            //             'reward_type' => 'AIRTIME',
            //             'reference' => time(), //$transfer['data']['reference'],
            //             'transfer_code' => time(),//$transfer['data']['transfer_code'],
            //             'recipient' => time(), //$airtime->responses['phoneNumber']
            //             'status' => 'success', //$airtime->responses['status'], 
            //             'currency' => "NGN"
            //         ]);
            //         return redirect('score/list')->with('status', 'Airtime Successfully Sent to your Number');
            // }else{
            //    return redirect('score/list')->with('error', 'There was an error while sending airtime, please try again later'); 
            // }

        }else{
            return 'nothing dey happen';
        }

    }

    public function saveBankInformation(Request $request)
    {
        $this->validate($request, [
            'account_number' => 'numeric|required'
        ]);
        $accountInformation = PaystackHelpers::resolveBankName($request->account_number, $request->bank_code);

        if($accountInformation['status'] == 'true')
        {
             $recipientCode = PaystackHelpers::recipientCode($accountInformation['data']['account_name'], $request->account_number, $request->bank_code);
                BankInformation::create([
                    'user_id' => auth()->user()->id,
                    'name' => $accountInformation['data']['account_name'],
                    'bank_name' => $recipientCode['data']['details']['bank_name'],
                    'account_number' => $request->account_number,
                    'bank_code' => $request->bank_code,
                    'recipient_code' => $recipientCode['data']['recipient_code'],
                    'currency' => 'NGN'
                ]);
                return redirect('wallet/withdraw')->with('success', 'Withdrawal Successfully queued');
        }else{
            return back()->with('error', 'Your bank account is not valid');
        }
       
    }

    public function transferFund($amount, $recipient)
    {
           return $fundTransfer = PaystackHelpers::transferFund($amount, $recipient);
    }

    public function sendAirtime($phone, $amount)
    {
        $bearerToken = PaystackHelpers::reloadlyAuth0Token();
        $bearerToken['access_token'];

        $operator = PaystackHelpers::getRealoadlyMobileOperator($bearerToken['access_token'], $phone);
        $operatorId = $operator['operatorId'];

        return PaystackHelpers::initiateReloadlyAirtime($bearerToken['access_token'], $phone, $operatorId, $amount);

        //return PaystackHelpers::reloadlyAuth0Token();

        // $username = "solotob";
        //  $apiKey = env('AFRICA_TALKING_LIVE');

        //  $AT = new AfricasTalking($username, $apiKey);

        //  $airtime = $AT->airtime();

        //  // Use the service
        //  $recipients = [[
        //      "phoneNumber"  => $phone,
        //      "currencyCode" => "NGN",
        //      "amount"       => $amount
        //  ]];

        //  try {
        //      // That's it, hit send and we'll take care of the rest
        //      $results = $airtime->send([
        //          "recipients" => $recipients
        //      ]);


        //  } catch(\Exception $e) {
        //      echo "Error: ".$e->getMessage();
        //  }
        //  return $results;//response()->json($results);  //json_decode($results->getBody()->getContents(), true);
    }
}
