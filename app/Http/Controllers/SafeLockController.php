<?php

namespace App\Http\Controllers;

use App\Mail\GeneralMail;
use App\Models\PaymentTransaction;
use App\Models\SafeLock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SafeLockController extends Controller
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
        $safeLocks = SafeLock::where('user_id', auth()->user()->id)->orderBy('created_at', 'DESC')->get();
        return view('user.safelock.index', ['safelocks' => $safeLocks]);
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
        // return $request;

        $interest_rate = 5;
        $amount_locked = $request->amount;
        $duration = $request->duration;
        $interest_accrued = 0.05 * $amount_locked;
        $total_payment = $amount_locked + $interest_accrued;
        $start_date = Carbon::now();
        $maturity_date = Carbon::now()->addMonth($request->duration);
        if($request->source == 'wallet'){

            if(auth()->user()->wallet->balance < $amount_locked){
                return back()->with('error', 'Insurficent balance, please top up wallet or use the paystack option');
            }
            
            debitWallet(auth()->user(), 'Naira', $amount_locked);
            $created = $this->createSafeLock($request, $interest_rate, $amount_locked, $duration, $interest_accrued, $total_payment, $start_date, $maturity_date);
            if($created){
                PaymentTransaction::create([
                    'user_id' => auth()->user()->id,
                    'campaign_id' => 1,
                    'reference' => time(),
                    'amount' => $amount_locked,
                    'status' => 'successful',
                    'currency' => 'NGN',
                    'channel' => 'internal',
                    'type' => 'safelock_created',
                    'description' => 'Created a SafeLock'
                ]);

                return back()->with('success', 'SafeLock Created Successfully');
            }

           
        }else{

        }
    }

    public function createSafeLock($request,$interest_rate, $amount_locked, $duration, $interest_accrued, $total_payment, $start_date, $maturity_date){
        $request->request->add(['user_id' => auth()->user()->id,'amount_locked' => $amount_locked, 'interest_rate' => $interest_rate, '$duration' => $duration, 'interest_accrued' => $interest_accrued, 'total_payment' => $total_payment, 'start_date' => $start_date, 'maturity_date' => $maturity_date]);
        $safeLockCreated = SafeLock::create($request->all());
        $subject = 'SafeLock Created';
        $content = 'Your SafeLock has been created successfully with a total amount of NGN'.$amount_locked.' which span for a period of '. $duration.' months at an interest of '.$interest_rate.'% giving a total pay out of NGN'.$total_payment.' on '.$maturity_date.'.'; //auth()->user()->name.' submitted a response to the your campaign - '.$campaign->post_title.'. Please login to review.';
        Mail::to(auth()->user()->email)->send(new GeneralMail(auth()->user(), $content, $subject, ''));
        return $safeLockCreated; 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
