<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Shop;
use Auth;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $quotations = Quotation::where('seller_id', Auth::user()->id)->get();

        $categoryNames = $quotations->pluck('product.category.name');

        $quotations = $quotations->map(function ($quotation, $key) use ($categoryNames) {
            $quotation->categoryName = $categoryNames[$key];
            return $quotation;
        });

        if (Auth::user()->user_type == "staff")
        {
            $roles = Auth()->user()->roles;
            $id = $roles[0]->created_by;

            $user = User::with(['shop'])->find($id);

            Auth::user()->shop = $user->shop;
            Auth::user()->owner_id = $id;
        }
        else
        {
            $id = Auth::user()->id;
        }
        $data['products'] = filter_products(Product::where('user_id', $id)->orderBy('num_of_sale', 'desc'))->limit(12)->get();
        $data['categories'] = Category::all();

        $data['last_7_days_sales'] = Order::where('created_at', '>=', Carbon::now()->subDays(7))
                                ->where('seller_id', '=', $id)
                                ->where('delivery_status', '=', 'delivered')
                                ->select(DB::raw("sum(grand_total) as total, DATE_FORMAT(created_at, '%d %b') as date"))
                                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
                                ->get()->pluck('total', 'date');

        return view('seller.dashboard', $data, compact('quotations'));
    }

    public function package_upgrde(Request $request)
    {
        $purchaseDate = date('Y-m-d');
        $subscription_plan = SubscriptionPlan::find($request->subscription_plan);

        $subscription = new Subscription();
        $subscription->user_id = Auth::user()->id;
        $subscription->plan_id = $subscription_plan->id;
        $subscription->purchase_at = $purchaseDate;
        // $subscription->valid_upto = date('Y-m-d', strtotime($purchaseDate." + ".$subscription_plan->duration." days"));
        $subscription->valid_upto = date('Y-m-d', strtotime($purchaseDate." + ".($subscription_plan->duration - 1)." days"));
        $subscription->order_id = "TB".substr(time(), 6).rand(10, 99);
        $subscription->amount = $subscription_plan->price;

        if ($subscription_plan->price == 0)
        {
            $subscription->status = "S";

            $shop = Shop::where('user_id', Auth::user()->id)->first();
            $shop->product_upload_limit = $subscription_plan->product_limit;
            $shop->save();

            $poducts = DB::table('products')->where('user_id', Auth::user()->id)->where('published', 1)->orderBy('id', 'desc')->get();
            if(!is_null($poducts))
            {
                for ($i=$subscription_plan->product_limit; $i < count($poducts); $i++)
                {
                    DB::table('products')->where('id', $poducts[$i]->id)->update(['published' => 0]);
                }
            }
        }
        else
        {
            $subscription->gateway = "razorpay";
        }

        $subscription->save();

        if($subscription->amount > 0)
        {
            $record = array(
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone,
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

            flash(translate('Package updated successfully!'))->success();
            return redirect()->route('seller.profile.index');
        }
    }
}
