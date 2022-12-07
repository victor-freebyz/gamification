<?php

namespace App\Http\Controllers;

use App\Mail\ApproveCampaign;
use App\Mail\CreateCampaign;
use App\Mail\GeneralMail;
use App\Mail\SubmitJob;
use App\Models\Campaign;
use App\Models\CampaignWorker;
use App\Models\Category;
use App\Models\PaymentTransaction;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CampaignController extends Controller
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
        $campaignList = Campaign::where('user_id', auth()->user()->id)->get();
        return view('user.campaign.index', ['lists' => $campaignList]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.campaign.create');
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
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $campaign = Campaign::where('job_id', $id)->first();
        return view('user.campaign.edit', ['campaign' => $campaign]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Campaign $campaign)
    {
        //return $request; 
        
        $ref = time();
        
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('PAYSTACK_SECRET_KEY')
        ])->post('https://api.paystack.co/transaction/initialize', [
            'email' => auth()->user()->email,
            'amount' => $request->total_amount_pay*100,
            'channels' => ['card'],
            'currency' => 'NGN',
            'reference' => $ref,
            'metadata' => ['number_of_staff' => $request->number_of_staff, 'total_amount' => $request->total_amount_pay],
            'callback_url' => env('PAYSTACK_CALLBACK_URL').'/extend/payment'
        ]);

        $url = $res['data']['authorization_url'];

        $camp = Campaign::where('id', $request->post_id)->first();
        $camp->extension_references = $ref;
        // $camp->number_of_staff += $request->number_of_staff;
        // $camp->total_amount += $request->total_amount_pay;
        $camp->save();
        
        PaymentTransaction::create([
            'user_id' => auth()->user()->id,
            'campaign_id' => $request->post_id,
            'reference' => $ref,
            'amount' => $request->total_amount_pay,
            'status' => 'unsuccessful',
            'currency' => 'NGN',
            'channel' => 'paystack',
            'type' => 'edit_campaign_payment',
            'description' => 'Extend Campaign Payment'
        ]);
        return redirect($url);
        
    }

    public function campaign_extension_payment(){
        $url = request()->fullUrl();
        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);
        $ref = $params['trxref'];
        
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('PAYSTACK_SECRET_KEY')
        ])->get('https://api.paystack.co/transaction/verify/'.$ref)->throw();
       $meta = $res['data']['metadata'];
       //$meata['number_of_staff'];
       $status = $res['data']['status'];
       if($status == 'success'){
            $fetchPaymentTransaction = PaymentTransaction::where('reference', $ref)->first();
            $fetchPaymentTransaction->status = 'successful';
            $fetchPaymentTransaction->save();

            $camp = Campaign::where('extension_references', $ref)->first();
            $camp->extension_references = null;
            $camp->number_of_staff += $meta['number_of_staff'];
            $camp->total_amount += $meta['total_amount'];
            $camp->save();

            return redirect('my/campaigns')->with('success', 'Campaign Successfully Edited');

       }
       return redirect('my/campaigns')->with('error', 'An Error occoured while editing campaign');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campaign $campaign)
    {
        //
    }

    public function getCategories()
    {
        return $categories = Category::orderBy('name', 'ASC')->get();
    }
    public function getSubCategories($id)
    {
        return $subCategories = SubCategory::where('category_id', $id)->orderBy('name', 'DESC')->get();
    }
    public function getSubcategoriesInfo($id)
    {
        return $subCategoriesInfo = SubCategory::where('id', $id)->first();
    }

    public function postCampaign(Request $request)
    {
        $est_amount = $request->number_of_staff * $request->campaign_amount;
        $percent = (50 / 100) * $est_amount;
        $total = $est_amount + $percent;
        // [$est_amount, $percent, $total];
        $job_id = rand(10000,10000000);
        $wallet = Wallet::where('user_id', auth()->user()->id)->first();
        //check if the bonus balance is valid
        if($wallet->bonus >= $total){
            $wallet->bonus -= $total;
            $wallet->save();
            $campaign = $this->processCampaign($total, $request, $job_id, $wallet,$percent);
            Mail::to(auth()->user()->email)->send(new CreateCampaign($campaign));
            return back()->with('success', 'Campaign Posted Successfully');
        }elseif($wallet->balance >= $total){
            $wallet->balance -= $total;
            $wallet->save();
            $campaign = $this->processCampaign($total, $request, $job_id, $wallet,$percent);
            Mail::to(auth()->user()->email)->send(new CreateCampaign($campaign));
            return back()->with('success', 'Campaign Posted Successfully');
        }else{
            return back()->with('error', 'You do not have suficient funds in your wallet');
        }  
    }

    public function processCampaign($total, $request, $job_id, $wallet, $percent)
    {
        $request->request->add(['user_id' => auth()->user()->id,'total_amount' => $total, 'job_id' => $job_id]);
        $campaign = Campaign::create($request->all());
        // $campaign->status = 'Live';
        // $campaign->save();

        $ref = time();
            PaymentTransaction::create([
                'user_id' => auth()->user()->id,
                'campaign_id' => $campaign->id,
                'reference' => $ref,
                'amount' => $total,
                'status' => 'successful',
                'currency' => 'NGN',
                'channel' => 'paystack',
                'type' => 'campaign_posted',
                'description' => $campaign->post_title.' Campaign'
            ]);
            $adminWallet = Wallet::where('user_id', '1')->first();
            $adminWallet->balance += $percent;
            $adminWallet->save();
             //Admin Transaction Tablw
             PaymentTransaction::create([
                'user_id' => 1,
                'campaign_id' => '1',
                'reference' => $ref,
                'amount' => $percent,
                'status' => 'successful',
                'currency' => 'NGN',
                'channel' => 'paystack',
                'type' => 'campaign_revenue',
                'description' => 'Campaign revenue from '.auth()->user()->name,
                'tx_type' => 'Credit',
                'user_type' => 'admin'
            ]);
            return $campaign;
    }

    public function viewCampaign($job_id)
    {
        $getCampaign = Campaign::where('job_id', $job_id)->first();
        $completed = CampaignWorker::where('user_id', auth()->user()->id)->where('campaign_id', $getCampaign->id)->first();
        return view('user.campaign.view', ['campaign' => $getCampaign, 'completed' => $completed]);
    }

    public function postCampaignWork(Request $request)
    {
       
        $check = CampaignWorker::where('user_id', auth()->user()->id)->where('campaign_id', $request->campaign_id)->first();
        if($check){
            return back()->with('error', 'You have comppleted this campaign before');
        }

        $campaignWorker = CampaignWorker::create($request->all());
        Mail::to(auth()->user()->email)->send(new SubmitJob($campaignWorker)); //send email to the member
        $campaign = Campaign::where('id', $request->campaign_id)->first();
        $user = User::where('id', $campaign->user->id)->first();
        $subject = 'Job Submission';
        $content = auth()->user()->name.' submitted a response to the your campaign - '.$campaign->post_title.'. Please login to review.';
        Mail::to($user->email)->send(new GeneralMail($user, $content, $subject));
    
        return back()->with('success', 'Job Submitted Successfully');
    }

    public function mySubmittedCampaign($id)
    {
        $work = CampaignWorker::where('id', $id)->first();
        if(!$work)
        {
            return redirect('home');
        }
        return view('user.campaign.my_submitted_campaign', ['work' => $work]);
    }

    public function activities($id)
    {
       $cam = Campaign::where('job_id', $id)->where('user_id', auth()->user()->id)->first();
        if(!$cam){
            return redirect('home');
        }
       return view('user.campaign.activities', ['lists' => $cam]);
    }

    public function campaignDecision(Request $request){
        $request->validate([
            'reason' => 'required|string',
        ]);
        if($request->action == 'approve'){
            $approve = CampaignWorker::where('id', $request->id)->first();
            $approve->status = 'Approved';
            $approve->reason = $request->reason;
            $approve->save();
     
            $wallet = Wallet::where('user_id', $approve->user_id)->first();
            $wallet->balance += $approve->amount;
            $wallet->save();
            $ref = time();
            PaymentTransaction::create([
                'user_id' => $approve->user_id,
                'campaign_id' => '1',
                'reference' => $ref,
                'amount' => $approve->amount,
                'status' => 'successful',
                'currency' => 'NGN',
                'channel' => 'paystack',
                'type' => 'campaign_payment',
                'description' => 'Campaign Payment for '.$approve->campaign->post_title,
                'tx_type' => 'Credit',
                'user_type' => 'regular'
            ]);
            $subject = 'Job Approved';
            $status = 'Approved';
            Mail::to($approve->user->email)->send(new ApproveCampaign($approve, $subject, $status));
            return back()->with('success', 'Campaign Approve Successfully');
        }else{
            $deny = CampaignWorker::where('id', $request->id)->first();
            $deny->status = 'Denied';
            $deny->reason = $request->reason;;
            $deny->save();
            $subject = 'Job Denied';
            $status = 'Denied';
            Mail::to($deny->user->email)->send(new ApproveCampaign($deny, $subject, $status));
            return back()->with('error', 'Campaign Denied Successfully');
        }
    }

    public function approveCampaign($id)
    {

       $approve = CampaignWorker::where('id', $id)->first();
       $approve->status = 'Approved';
       $approve->reason = 'Approved by User';
       $approve->save();

       $wallet = Wallet::where('user_id', $approve->user_id)->first();
       $wallet->balance += $approve->amount;
       $wallet->save();
       $ref = time();
       PaymentTransaction::create([
        'user_id' => $approve->user_id,
        'campaign_id' => '1',
        'reference' => $ref,
        'amount' => $approve->amount,
        'status' => 'successful',
        'currency' => 'NGN',
        'channel' => 'paystack',
        'type' => 'campaign_payment',
        'description' => 'Campaign Payment for '.$approve->campaign->post_title,
        'tx_type' => 'Credit',
        'user_type' => 'regular'
    ]);

       $subject = 'Job Approved';
       $status = 'Approved';
       Mail::to($approve->user->email)->send(new ApproveCampaign($approve, $subject, $status));

       return back()->with('success', 'Campaign Approve Successfully');

    }

    public function denyCampaign($id)
    {
        $deny = CampaignWorker::where('id', $id)->first();
        $deny->status = 'Denied';
        $deny->reason = 'Denied by User';
        $deny->save();
        $subject = 'Job Denied';
        $status = 'Denied';
        Mail::to($deny->user->email)->send(new ApproveCampaign($deny, $subject, $status));

        return back()->with('error', 'Campaign Denied Successfully');
    }

    public function approvedCampaigns()
    {
        $approved = CampaignWorker::where('status', 'Approved')->orderby('created_at', 'ASC')->get();
        return view('user.campaign.approved', ['lists' => $approved]);
    }
    public function deniedCampaigns()
    {
        $denied = CampaignWorker::where('status', 'Denied')->orderby('created_at', 'ASC')->get();
        return view('user.campaign.denied', ['lists' => $denied]);
    }

    public function completedJobs()
    {
        $completedJobs = CampaignWorker::where('user_id', auth()->user()->id)->orderBy('created_at', 'ASC')->get();
        return view('user.campaign.completed_jobs', ['lists' => $completedJobs]);
    }

    
}
