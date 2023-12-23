<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerRegistrationRequest;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\BusinessSetting;
use App\Models\Address;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Auth;
use Hash;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\SellerDelete;


class ShopController extends Controller
{

    public function __construct()
    {
        $this->middleware('user', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user()->shop;
        return view('seller.shop', compact('shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::check()) {
			if((Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'customer')) {
				flash(translate('Admin or Customer can not be a seller'))->error();
				return back();
			} if(Auth::user()->user_type == 'seller'){
				flash(translate('This user already a seller'))->error();
				return back();
			}

        } else {
            $subscriptionPlans = SubscriptionPlan::where('delete_flag', 0)->orderBy('price', 'asc')->get();

            return view('frontend.seller_form', compact('subscriptionPlans'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(SellerRegistrationRequest $request)
    public function store(Request $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_type = "seller";
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            $admin = User::where('user_type', 'admin')->first();

            $body = "🌟 New Registration on ".env('APP_NAME')."! 🌟<br>
                We've got a new member in our community!<br>
                Details: <br>
                Name: ". $user->name ." <br>
                Username/ID: ".$user->email." <br>
                Email Address: ".$user->email." <br>
                Shop Name (if applicable): ".$request->shop_name." <br>
                Registration Date: ".date('d F, Y');

            sendAdminNotification($admin->id, 'seller_registration', null, null, null, $body);

            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->address;
            $shop->city_id = $request->city;
            $shop->gst_number = $request->gst_number;
            $shop->gst_number_status = "Not Verified";
            $shop->slug = preg_replace('/\s+/', '-', str_replace("/"," ", $request->shop_name));
            $shop->save();

            auth()->login($user, false);

            if (BusinessSetting::where('type', 'email_verification')->first()->value != 1)
            {
                $user->email_verified_at = date('Y-m-d H:m:s');
                $user->save();
            } else {
                $user->notify(new EmailVerificationNotification());
            }


            // SUBSCRIPTION //
            if (!is_null($request->subscription_plan) || !empty($request->subscription_plan))
            {
                $purchaseDate = date('Y-m-d');
                $subscription_plan = SubscriptionPlan::find($request->subscription_plan);

                $subscription = new Subscription();
                $subscription->user_id = $user->id;
                $subscription->plan_id = $subscription_plan->id;
                $subscription->purchase_at = $purchaseDate;
                //$subscription->valid_upto = date('Y-m-d', strtotime($purchaseDate." + ".$subscription_plan->duration." days"));
                $subscription->valid_upto = date('Y-m-d', strtotime($purchaseDate." + ".($subscription_plan->duration - 1)." days"));
                $subscription->buffer_upto = date('Y-m-d', strtotime($subscription->valid_upto." + ".$subscription_plan->buffer_days." days"));
                $subscription->product_limit = $subscription_plan->product_limit;
                $subscription->order_id = "TB".substr(time(), 6).rand(10, 99);
                $subscription->amount = $subscription_plan->price;

                if ($subscription_plan->price == 0 || $subscription_plan->is_default == 1)
                {
                    $subscription->status = "S";

                    $shop->product_upload_limit = $subscription_plan->product_limit;
                    $shop->save();
                    if($shop){
                        $user = User::find(Auth::user()->id);
                        $user->free_plan = 1;
                        $user->save();
                    }
                }
                else
                {
                    $subscription->gateway = 'razorpay';
                }

                $subscription->save();
            }
            // SUBSCRIPTION //

            // $notificationData = [
            //     'name' => $shop->user->name,
            //     'body' => Config('notification.seller_register'),
            //     'thanks' => 'Thank you'
            // ];

            // try {
            //     \Notification::send($shop->user, new SellerDelete($notificationData));
            // } catch (Exception $e) {
            //     // dd($e);
            //     echo "<script>console.log('".$e."')</script>";
            //     // return back();
            // }

            if(!is_null($request->subscription_plan) || !empty($request->subscription_plan))
            {
                if ($subscription->amount > 0 && $subscription_plan->is_default != 1)
                {
                    $record = array(
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'subscription' => $subscription
                    );
                    return view('seller.subscription.payment-gateway', $record);
                }
                else
                {
                    $body = "🎉 Subscription Successful! 🎉<br>
                            Congratulations! You've successfully purchased a new subscription plan on ".env('APP_NAME').".<br>
                            Plan Details: <br>
                            &emsp;1) Name: ".$subscription_plan->title.",<br>
                            &emsp;2) Duration: ".$subscription_plan->duration." Days,<br>
                            &emsp;3) Start Date: ".date("d F, Y", strtotime($subscription->purchase_at)).",<br>
                            &emsp;4) End Date: ".date("d F, Y", strtotime($subscription->valid_upto)).".<br>
                            Benefits Unlocked:<br>
                            Dive into your seller dashboard to explore all the new features and benefits.<br>
                            👉 <a href='".url('seller/dashboard')."' class='btn btn-primary btn-sm'>Go to Dashboard</a>. <br>
                            Thank you for choosing ".env('APP_NAME').". Let's achieve great things together!";

                    sendSellerNotification(Auth::user()->id, "seler_subscription", null, null, null, $body);

                    $body = "💰 New Subscription Payment Received! 💰<br>
                            Good news! A payment for a subscription plan has just been processed on ".env('APP_NAME').".<br>
                            Seller Details: <br>
                            &emsp;1) Name: ".Auth::user()->name.", <br>
                            &emsp;2) ID/Username: ".Auth::user()->email.".<br>

                            Plan Purchased: <br>
                            &emsp;1) Name: ".$subscription_plan->title.",, <br>
                            &emsp;2) Duration: ".$subscription_plan->duration." Days,<br>
                            &emsp;3) Amount: ".$subscription_plan->price.", <br>
                            &emsp;4) Transaction ID: ".$subscription->payment_id."<br>
                            👉 <a href='".url('admin/subscriptions/payments')."' class='btn btn-primary'>Go to Dashboard</a>";

                    $admin = User::where('user_type', 'admin')->first();
                    sendAdminNotification($admin->id, "admin_subscription", null, null, null, $body);

                    flash(translate('Your Shop has been created successfully!'))->success();
                    return redirect()->route('seller.shop.index');
                }
            }
            else
            {
                flash(translate('Your Shop has been created successfully!'))->success();
                return redirect()->route('seller.shop.index');
            }
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
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

    public function destroy($id)
    {
        //
    }
}
