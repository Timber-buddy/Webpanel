<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Shop;
use App\Models\User;
use Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $subscriptions = SubscriptionPlan::where('delete_flag', 0)->orderBy('id', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $subscriptions = $subscriptions->where('title', 'like', '%' . $sort_search . '%')->OrWhere('duration', 'like', '%' . $sort_search . '%');
        }
        $subscriptions = $subscriptions->paginate(10);


        //$subscriptions = SubscriptionPlan::where('delete_flag', 0)->orderBy('id', 'desc')->paginate(15);
        return view('backend.subscriptions.index', compact('subscriptions', 'sort_search'));
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
        $plan = new SubscriptionPlan();
        $plan->title = $request->title;
        $plan->duration = $request->duration;
        $plan->price = $request->price;
        $plan->description = $request->description;
        $plan->product_limit = $request->product_limit;

        $fileName = time().'.'.$request->file('image')->extension();
        $path = 'assets/uploads/subscription';
        $request->file('image')->move(public_path($path), $fileName, 'public');

        $plan->image = $path.'/'.$fileName;
        $plan->save();

        $sellers = User::select('id')->where('user_type', 'seller')->get();

        $body = "🌟 New Plan Alert! 🌟 <br>
            Admin has just introduced a new plan on ".env('APP_NAME').". <br>
            Plan Name: ".$plan->title.", <br>
            Duration: ".$plan->duration." Days, <br>";
            if($plan->price == 0)
            {
                $body .= "Price: Free<br>";
            }
            else
            {
                $body .= 'Price: '.number_format($plan->price, 2)."<br>";
            }
            $body .= "Top Features. Interested? Dive in to learn more and see how this plan can benefit you. <br>
            <a href='".url('seller/profile')."'>View Plan Details</a>";

        foreach ($sellers as $seller) {
            sendSellerNotification($seller->id, "seler_subscription_new", null, null, null, $body);
        }

        $body = "✅ Plan Creation Successful!<br>
            You've successfully added a new plan to ".env('APP_NAME').".<br>
            Plan Name: ".$plan->title.", <br>
            Duration: ".$plan->duration." Days, <br>";
        if($plan->price == 0)
        {
            $body .= "Price: Free<br>";
        }
        else
        {
            $body .= 'Price: '.number_format($plan->price, 2)."<br>";
        }
        $body .= "Key Features: Product limit - ".$plan->product_limit."<br>
            Please review the details to ensure accuracy. If any modifications are needed,
            head to the 'Plans Management' section in your dashboard.";

        sendAdminNotification(Auth::user()->id, "admin_subscription_new", null, null, null, $body);

        flash(translate('Subscription plan has been created successfully'))->success();
        return redirect()->route('subscriptions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = SubscriptionPlan::find($id);
        $record = array(
            'plan' => $plan,
            'url' => route('subscriptions.update', $id)
        );

        return $record;
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
        $plan = SubscriptionPlan::find($id);
        $plan->title = $request->title;
        $plan->duration = $request->duration;
        $plan->price = $request->price;
        $plan->description = $request->description;
        $plan->product_limit = $request->product_limit;

        if($request->hasFile('image'))
        {
            $fileName = time().'.'.$request->file('image')->extension();
            $path = 'assets/uploads/subscription';
            $request->file('image')->move(public_path($path), $fileName, 'public');

            $plan->image = $path.'/'.$fileName;
        }

        $plan->save();

        flash(translate('Subscription plan has been updated successfully'))->success();
        return redirect()->route('subscriptions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plan = SubscriptionPlan::find($id);

        if(!is_null($plan))
        {
            $plan->delete_flag = 1;
            $plan->save();
        }

        flash(translate('Subscription plan has been deleted successfully'))->success();
        return redirect()->route('subscriptions.index');
    }

    public function payment_history(Request $request)
    {
        $sort_search = null;
            $subscriptions = Subscription::with(['user:id,name', 'plan:id,title'])
                ->orderBy('id', 'desc');

            if ($request->has('search')) {
                $sort_search = $request->search;

                $subscriptions = $subscriptions->whereHas('user', function ($query) use ($sort_search) {
                    $query->where('name', 'like', '%' . $sort_search . '%');
                })->orWhere('amount', 'like', '%' . $sort_search . '%');
            }

            $subscriptions = $subscriptions->paginate(10);

        // $sort_search = null;
        // $subscriptions = Subscription::with(['user:id,name', 'plan:id,title'])->orderBy('id', 'desc')->paginate(15);

        return view('backend.subscriptions.purchase-history', compact('subscriptions', 'sort_search'));
    }

    public function payment_history_delete($id)
    {
        $transaction = Subscription::find($id);

        if(!is_null($transaction))
        {
            $transaction->status = 'F';
            $transaction->save();
        }

        flash(translate('Subscription status has been updated successfully'))->success();
        return redirect()->route('subscription.payments');
    }

    public function payment_history_update(Request $request, $id)
    {
        $transaction = Subscription::find($id);

        if(!is_null($transaction))
        {
            $transaction->status = $request->status;
            $transaction->save();

            if ($request->status == "S")
            {
                $subscription_plan = SubscriptionPlan::find($transaction->plan_id);
                $shop = Shop::where('user_id', $transaction->user_id)->first();

                $shop->product_upload_limit = $subscription_plan->product_limit;
                $shop->save();
            }
        }

        flash(translate('Subscription status has been updated successfully'))->success();
        return redirect()->route('subscription.payments');
    }
}
