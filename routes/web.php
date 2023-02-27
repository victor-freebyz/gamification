<?php



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// use Illuminate\Support\Facades\App;

Route::get('/', [\App\Http\Controllers\GeneralController::class, 'landingPage']);
Route::get('contact', [\App\Http\Controllers\GeneralController::class, 'contact'])->name('contact');
Route::get('goal', [\App\Http\Controllers\GeneralController::class, 'goal'])->name('goal');
Route::get('games', [\App\Http\Controllers\GeneralController::class, 'gamelist'])->name('game.list');
Route::get('winner/list', [\App\Http\Controllers\GeneralController::class, 'winnerlist'])->name('winner.list');
Route::get('register/{referral_code}', [\App\Http\Controllers\Auth\RegisterController::class, 'referral_register']);
Route::get('make-money', [\App\Http\Controllers\GeneralController::class, 'make_money']);
Route::get('terms', [\App\Http\Controllers\GeneralController::class, 'terms'])->name('terms');
Route::get('privacy', [\App\Http\Controllers\GeneralController::class, 'privacy'])->name('privacy');
Route::get('track-record', [\App\Http\Controllers\GeneralController::class, 'trackRecord'])->name('track.record');
Route::get('faq', [\App\Http\Controllers\GeneralController::class, 'faq'])->name('faq');
Route::get('download', [\App\Http\Controllers\GeneralController::class, 'download']);//->name('faq');
Route::post('download', [\App\Http\Controllers\GeneralController::class, 'download_url'])->name('download');

Route::get('marketplace/payment/callback', [\App\Http\Controllers\GeneralMarketplaceController::class, 'marketPlacePaymentCallBack']);
Route::get('marketplace/payment/completion', [\App\Http\Controllers\GeneralMarketplaceController::class, 'marketplaceCompletePayment']);

Route::get('marketplace/{referral_code}/{product_id}', [\App\Http\Controllers\GeneralMarketplaceController::class, 'index']);//->name('marketplace');
Route::get('marketplace/payment/{referral_code}/{product_id}/{ref}', [\App\Http\Controllers\GeneralMarketplaceController::class, 'processPayment']);//->name('marketplace');
Route::post('marketplace/proccess/payment', [\App\Http\Controllers\GeneralMarketplaceController::class, 'enter_info']);//->name('marketplace');
Route::get('resource/{url}', [\App\Http\Controllers\GeneralMarketplaceController::class, 'resourceDownload']);


Auth::routes();

Route::get('auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

//Take Quiz
Route::get('instruction', [\App\Http\Controllers\HomeController::class, 'instruction'])->name('instruction');
Route::get('take/quiz', [\App\Http\Controllers\HomeController::class, 'takeQuiz'])->name('take.quiz');
Route::get('next/question', [\App\Http\Controllers\HomeController::class, 'nextQuestion']);
Route::get('submit/answers', [\App\Http\Controllers\HomeController::class, 'submitAnswers']);
Route::post('store/asnwer', [\App\Http\Controllers\HomeController::class, 'storeAnswer'])->name('store.answer');
Route::get('score/list', [\App\Http\Controllers\HomeController::class, 'scores'])->name('score.list');
Route::get('redeem/reward/{id}', [\App\Http\Controllers\HomeController::class, 'redeemReward'])->name('redeem.reward');
Route::post('save/bank/information', [\App\Http\Controllers\HomeController::class, 'saveBankInformation'])->name('save.bank.information');
Route::post('save/phone/information', [\App\Http\Controllers\HomeController::class, 'savePhoneInformation'])->name('save.phone.information');


////Referral Routes
Route::get('referral/view/all', [\App\Http\Controllers\ReferralController::class, 'viewAll'])->name('ref.all');
////Campaign
Route::get('campaign/create', [\App\Http\Controllers\CampaignController::class, 'create'])->name('campaign.create');
Route::get('api/get/categories', [\App\Http\Controllers\CampaignController::class, 'getCategories']);
Route::get('api/get/sub/categories/{id}', [\App\Http\Controllers\CampaignController::class, 'getSubCategories']);
Route::get('api/get/sub/categories/info/{id}', [\App\Http\Controllers\CampaignController::class, 'getSubcategoriesInfo']);
Route::post('post/campaign', [\App\Http\Controllers\CampaignController::class, 'postCampaign'])->name('post.campaign');
Route::post('edit/campaign', [\App\Http\Controllers\CampaignController::class, 'update'])->name('edit.campaign');
Route::get('extend/payment', [\App\Http\Controllers\CampaignController::class, 'campaign_extension_payment']);
Route::get('campaign/{job_id}', [\App\Http\Controllers\CampaignController::class, 'viewCampaign']);
Route::post('post/campaign/work', [\App\Http\Controllers\CampaignController::class, 'postCampaignWork'])->name('post.campaign.work');
Route::get('my/jobs', [\App\Http\Controllers\JobsController::class, 'myJobs'])->name('my.jobs');
Route::get('my/campaigns', [\App\Http\Controllers\CampaignController::class, 'index'])->name('my.campaigns');
Route::get('campaign/my/submitted/{id}', [\App\Http\Controllers\CampaignController::class, 'mySubmittedCampaign']);
Route::get('campaign/activities/{id}', [\App\Http\Controllers\CampaignController::class, 'activities']);
Route::post('campaign/decision', [\App\Http\Controllers\CampaignController::class, 'campaignDecision'])->name('campaign.decision');
Route::get('campaign/{id}/edit', [\App\Http\Controllers\CampaignController::class, 'edit']);
///paystack payment 
Route::post('/pay', [App\Http\Controllers\PaymentController::class, 'redirectToGateway'])->name('pay');
Route::get('/payment/callback', [\App\Http\Controllers\PaymentController::class, 'handleGatewayCallback']);

///payment routes
Route::get('golive/{job_id}', [\App\Http\Controllers\PaystackPaymentController::class, 'goLive']);
Route::get('callback', [\App\Http\Controllers\PaystackPaymentController::class, 'paystackCallback']);

Route::get('upgrade', [\App\Http\Controllers\UserController::class, 'upgrade'])->name('upgrade');
Route::get('make/payment', [\App\Http\Controllers\UserController::class, 'makePayment'])->name('make.payment');
Route::get('upgrade/payment', [\App\Http\Controllers\UserController::class, 'upgradeCallback']);
Route::get('make/payment/wallet', [\App\Http\Controllers\UserController::class, 'makePaymentWallet'])->name('make.payment.wallet');

Route::get('success', [\App\Http\Controllers\UserController::class, 'success']);
Route::get('error', [\App\Http\Controllers\UserController::class, 'error']);
Route::get('transactions', [\App\Http\Controllers\UserController::class, 'transactions'])->name('transactions');
Route::get('withrawal/requests', [\App\Http\Controllers\UserController::class, 'withdrawal_requests'])->name('withdraw.requests');

Route::get('approved/campaigns', [\App\Http\Controllers\CampaignController::class, 'approvedCampaigns']);
Route::get('denied/campaigns', [\App\Http\Controllers\CampaignController::class, 'deniedCampaigns']);
Route::get('completed/jobs', [\App\Http\Controllers\CampaignController::class, 'completedJobs']);

Route::post('addmore/workers', [\App\Http\Controllers\CampaignController::class, 'addMoreWorkers'])->name('addmore.workers');


Route::get('campaign/approve/{id}', [\App\Http\Controllers\CampaignController::class, 'approveCampaign']);
Route::get('campaign/deny/{id}', [\App\Http\Controllers\CampaignController::class, 'denyCampaign']);

Route::get('wallet/fund', [\App\Http\Controllers\WalletController::class, 'fund'])->name('fund');
Route::get('wallet/withdraw', [\App\Http\Controllers\WalletController::class, 'withdraw'])->name('withdraw');
Route::post('store/fund', [\App\Http\Controllers\WalletController::class, 'storeFund'])->name('store.funds');
Route::post('store/withdraw', [\App\Http\Controllers\WalletController::class, 'storeWithdraw'])->name('store.withdraw');
Route::get('wallet/topup', [\App\Http\Controllers\WalletController::class, 'walletTop']);
Route::get('airtime', [\App\Http\Controllers\UserController::class, 'airtimePurchase'])->name('airtime');
Route::post('buy/airtime', [\App\Http\Controllers\UserController::class, 'buyAirtime'])->name('buy.airtime');
Route::get('databundle', [\App\Http\Controllers\UserController::class, 'databundlePurchase'])->name('databundle');
Route::post('buy/databundle', [\App\Http\Controllers\UserController::class, 'buyDatabundle'])->name('buy.databundle');

//Marketplace
Route::get('marketplace', [\App\Http\Controllers\MarketplaceController::class, 'index'])->name('marketplace');
Route::get('marketplace/view', [\App\Http\Controllers\MarketplaceController::class, 'createProduct'])->name('create.marketplace');
Route::post('marketplace/store', [\App\Http\Controllers\MarketplaceController::class, 'storeProduct'])->name('store.marketplace.product');
Route::get('marketplace/list', [\App\Http\Controllers\MarketplaceController::class, 'myProduct'])->name('my.marketplace.products');

Route::get('feedback', [\App\Http\Controllers\FeedbackController::class, 'index'])->name('feedback');
Route::post('feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('store.feedback');

// ------------------------------------ Admin Routes ------------------------------------------ 
//Admin Routes
Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index']);

Route::get('user/home', [\App\Http\Controllers\HomeController::class, 'userHome'])->name('user.home');
Route::get('admin/home', [\App\Http\Controllers\HomeController::class, 'adminHome'])->name('admin.home');

Route::get('/games/create/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'createGame']);
Route::get('question/create', [\App\Http\Controllers\Admin\AdminController::class, 'createQuestion'])->name('questions.create');
Route::post('question/store', [\App\Http\Controllers\Admin\AdminController::class, 'storeQuestion'])->name('questions.store');
Route::post('question/update', [\App\Http\Controllers\Admin\AdminController::class, 'updateQuestion'])->name('questions.update');
Route::get('question/list', [\App\Http\Controllers\Admin\AdminController::class, 'listQuestion'])->name('question.list');
//Game Routes
Route::get('game/status/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'gameStatus'])->name('game.status');
Route::get('view/activities/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'viewActivities'])->name('view.activities');
Route::post('assign/reward', [\App\Http\Controllers\Admin\AdminController::class, 'assignReward'])->name('assign.reward');



Route::get('game/create', [\App\Http\Controllers\Admin\AdminController::class, 'gameCreate'])->name('game.create');
Route::post('game/store', [\App\Http\Controllers\Admin\AdminController::class, 'gameStore'])->name('game.store');
Route::get('view/amount', [\App\Http\Controllers\Admin\AdminController::class, 'viewAmount'])->name('view.amount');
Route::post('update/amount', [\App\Http\Controllers\Admin\AdminController::class, 'updateAmount'])->name('update.amount');

//airtime mgt
//Route::get('airime', [App\Http\Controllers\Admin\AdminController::class, 'sendAirtime'])->name('airtime');

//category routes
Route::get('create/category', [\App\Http\Controllers\CategoryController::class, 'create'])->name('create.category');
Route::post('post/category', [\App\Http\Controllers\CategoryController::class, 'store'])->name('store');
Route::post('post/subcategory', [\App\Http\Controllers\CategoryController::class, 'storeSubcategory'])->name('store.subcategory');
//User List
Route::get('users', [\App\Http\Controllers\Admin\AdminController::class, 'userList'])->name('user.list');
Route::get('verified/users', [\App\Http\Controllers\Admin\AdminController::class, 'verifiedUserList'])->name('verified.user.list');
Route::get('admin/transaction', [\App\Http\Controllers\Admin\AdminController::class, 'adminTransaction'])->name('admin.transaction');
Route::get('user/transaction', [\App\Http\Controllers\Admin\AdminController::class, 'userTransaction'])->name('user.transaction');

Route::get('user/{id}/info', [\App\Http\Controllers\Admin\AdminController::class, 'userInfo']);
Route::get('admin/withdrawal/request', [\App\Http\Controllers\Admin\AdminController::class, 'withdrawalRequest'])->name('admin.withdrawal');
Route::get('update/withdrawal/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateWithdrawalRequest']);
Route::get('admin/upgrade/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'upgradeUser']);//->name('admin.withdrawal');

Route::get('campaigns', [\App\Http\Controllers\Admin\AdminController::class, 'campaignList'])->name('campaign.list');
Route::get('campaigns/pending', [\App\Http\Controllers\Admin\AdminController::class, 'campaignPending'])->name('campaign.pending');
Route::get('campaign/status/{status}/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'campaignStatus']);
Route::get('mass/mail', [\App\Http\Controllers\Admin\AdminController::class, 'massMail'])->name('mass.mail');
Route::post('send/mass/mail', [\App\Http\Controllers\Admin\AdminController::class, 'sendMassMail'])->name('send.mass.email');

Route::get('unapproved', [\App\Http\Controllers\Admin\AdminController::class, 'unapprovedJobs'])->name('unapproved');
Route::get('approved', [\App\Http\Controllers\Admin\AdminController::class, 'approvedJobs'])->name('approved');
Route::get('reverse/transaction/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'jobReversal']);
Route::post('mass/approval', [\App\Http\Controllers\Admin\AdminController::class, 'massApproval'])->name('mass.approval');

/////Market Place
Route::get('admin/marketplace/view', [\App\Http\Controllers\Admin\AdminController::class, 'viewMarketplace'])->name('view.admin.marketplace');
Route::get('admin/marketplace/create', [\App\Http\Controllers\Admin\AdminController::class, 'marketplaceCreateProduct'])->name('marketplace.create.product');
Route::post('admin/post/marketplace', [\App\Http\Controllers\Admin\AdminController::class, 'storeMarketplace'])->name('store.marketplace');
Route::get('admin/remove/marketplace/product/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'removeMarketplaceProduct']);
//Databundles
Route::get('create/databundles', [\App\Http\Controllers\Admin\AdminController::class, 'createDatabundles'])->name('create.databundles');
Route::post('store/databundles', [\App\Http\Controllers\Admin\AdminController::class, 'storeDatabundles'])->name('store.databundles');
Route::get('charts', [\App\Http\Controllers\Admin\AdminController::class, 'charts']);
Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
Route::post('store/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'store'])->name('store.settings');
Route::get('admin/feedback', [\App\Http\Controllers\Admin\FeedbackRepliesController::class, 'index'])->name('admin.feedback');
Route::get('admin/feedback/{id}', [\App\Http\Controllers\Admin\FeedbackRepliesController::class, 'view']);
Route::post('store/admin/feedback/', [\App\Http\Controllers\Admin\FeedbackRepliesController::class, 'store'])->name('store.admin.feedbackreplies');
Route::post('admin/store/fund', [\App\Http\Controllers\Admin\AdminController::class, 'adminWalletTopUp'])->name('admin.wallet.topup');
///// External Fintech Api 

Route::get('flutterwave/trf/list', [\App\Http\Controllers\Admin\AdminController::class, 'listFlutterwaveTrf']);


