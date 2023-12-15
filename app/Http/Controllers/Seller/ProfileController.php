<?php

namespace App\Http\Controllers\Seller;

use App\Http\Requests\SellerProfileRequest;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Shop;
use App\Models\BusinessSetting;
use App\Models\Product;
use Auth;
use Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Session;
use Exception;
use DB;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->user_type == "seller")
        {
            $addresses = $user->addresses;
            $subscriptionPlans = SubscriptionPlan::where('delete_flag', 0)->orderBy('price', 'asc')->get();
            $subscription = subscription::with('plan')->where('user_id', $user->id)->whereIn('status', ['C', 'S'])->orderBy('id', 'desc')->first();
            $active_product = DB::table('products')->where('user_id', $user->id)->where('published', 1)->orderBy('id', 'desc')->count();

            return view('seller.profile.index', compact('user','addresses','subscriptionPlans','subscription','active_product'));
        }

        return view('seller.profile.index', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SellerProfileRequest $request , $id)
    {
        if(env('DEMO_MODE') == 'On'){
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $flag = 0;
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->phone = $request->phone;

        if($request->new_password != null && ($request->new_password == $request->confirm_password)){
            $user->password = Hash::make($request->new_password);

            $flag = 1;
        }

        $user->avatar_original = $request->photo;

        $shop = $user->shop;

        if($shop){
            $shop->cash_on_delivery_status = $request->cash_on_delivery_status;
            $shop->bank_payment_status = $request->bank_payment_status;
            $shop->bank_name = $request->bank_name;
            $shop->bank_acc_name = $request->bank_acc_name;
            $shop->bank_acc_no = $request->bank_acc_no;
            $shop->bank_routing_no = $request->bank_routing_no;

            $shop->save();
        }

        $user->save();

        if($flag == 1)
        {
            $body = "🔒 Password Update Alert! 🔒<br>
                Hey ".auth()->user()->name.", We wanted to let you know that the password for your vendor account on ".env('APP_NAME')." was recently updated.<br>
                If you didn't authorize this password change, please take immediate action then Contact Support directly within the app";

            $business_settings = BusinessSetting::where('type', 'contact_email')->first();
            if (!is_null($business_settings))
            {
                $body .= ".";
            }
            else
            {
                $body .= " or at ".$business_settings->value." for further assistance.";
            }
            sendSellerNotification(auth()->user()->id, 'reset_password', null, null, null, $body);
        }

        flash(translate('Your Profile has been updated successfully!'))->success();
        return back();
    }

    public function process_payment(Request $request)
    {
        $input = $request->all();
        $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if(count($input)  && !empty($input['razorpay_payment_id']))
        {
            try
            {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount']));

                $record = Subscription::find($input['record_id']);
                $record->payment_id = $response->id;
                $record->method = $response->method;
                $record->currency = $response->currency;
                $record->json_response = json_encode($response->toArray());
                $record->status = 'S';
                $record->save();

                $subscription_plan = SubscriptionPlan::find($record->plan_id);

                $poducts = DB::table('products')->where('user_id', $record->user_id)->where('published', 1)->orderBy('id', 'desc')->get();
                if(!is_null($poducts))
                {
                    for ($i=$subscription_plan->product_limit; $i < count($poducts); $i++)
                    {
                        DB::table('products')->where('id', $poducts[$i]->id)->update(['published' => 0]);
                    }
                }

                $body = "🎉 Subscription Successful! 🎉<br>
                        Congratulations! You've successfully purchased a new subscription plan on ".env('APP_NAME').".<br>
                        Plan Details: <br>
                        &emsp;1) Name: ".$subscription_plan->title.",<br>
                        &ensp;2) Duration: ".$subscription_plan->duration." Days,<br>
                        &ensp;3) Start Date: ".$record->purchase_at.",<br>
                        &ensp;4) End Date: ".$record->valid_upto.".<br>
                        Benefits Unlocked:<br>
                        Dive into your seller dashboard to explore all the new features and benefits.<br>
                        👉 <a href='".url('seller/dashboard')."' class='btn btn-primary'>Go to Dashboard</a>. <br>
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
                        &emsp;4) Transaction ID: ".$record->payment_id."<br>
                        👉 <a href='".url('admin/subscriptions/payments')."' class='btn btn-primary'>Go to Dashboard</a>";

                $admin = User::where('user_type', 'admin')->first();
                sendAdminNotification($admin->id, "admin_subscription", null, null, null, $body);
            }
            catch (Exception $e)
            {
                flash(translate('Something went wrong!'))->error();
                return redirect(route("seller.profile.index"));
            }

            flash(translate('Payment successfully!'))->success();
            return redirect(route("seller.profile.index"));
        }
        else
        {
            flash(translate('Payment successfully!'))->success();
            return redirect(route("seller.profile.index"));
        }
    }

    public function planDetails($id)
    {
        $subscription = SubscriptionPlan::find($id);
        return view('ajax.plan-details', compact('subscription'));
    }

    public function process_payment_as_failed()
    {
        //dd('Failed');
        // $record = Subscription::where('user_id', Auth::user()->id)->where('status', 'P')->orderBy('id', 'desc')->first();
        // $record->status = 'F';
        // $record->save();

        // flash(translate('Payment failed!'))->error();
        return redirect(url("/seller/dashboard"));
    }
}
