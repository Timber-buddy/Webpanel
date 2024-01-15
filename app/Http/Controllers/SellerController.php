<?php

namespace App\Http\Controllers;

use Cache;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Notifications\SellerDelete;
use App\Notifications\SellerUpdate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SellerQuotationReplyMail;
use App\Mail\EmailManager;
use App\Notifications\EmailVerificationNotification;

class SellerController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_seller'])->only('index');
        $this->middleware(['permission:view_seller_profile'])->only('profile_modal');
        $this->middleware(['permission:login_as_seller'])->only('login');
        $this->middleware(['permission:pay_to_seller'])->only('payment_modal');
        $this->middleware(['permission:edit_seller'])->only('edit');
        $this->middleware(['permission:delete_seller'])->only('destroy');
        $this->middleware(['permission:ban_seller'])->only('ban');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $approved = null;
        $shops = Shop::whereIn('user_id', function ($query) {
            $query->select('id')
                ->from(with(new User)->getTable());
        })->latest();

        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'seller')->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')->orWhere('email', 'like', '%' . $sort_search . '%');
            })->pluck('id')->toArray();
            $shops = $shops->where(function ($shops) use ($user_ids) {
                $shops->whereIn('user_id', $user_ids);
            });
        }
        if ($request->approved_status != null) {
            $approved = $request->approved_status;
            $shops = $shops->where('verification_status', $approved);
        }
        $shops = $shops->paginate(15);
        return view('backend.sellers.index', compact('shops', 'sort_search', 'approved'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.sellers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (User::where('email', $request->email)->first() != null) {
            flash(translate('Email already exists!'))->error();
            return back();
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_type = "seller";
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            if (get_setting('email_verification') != 1) {
                $user->email_verified_at = date('Y-m-d H:m:s');
            } else {
                $user->notify(new EmailVerificationNotification());
            }
            $user->save();

            $seller = new Seller;
            $seller->user_id = $user->id;

            if ($seller->save()) {
                $shop = new Shop;
                $shop->user_id = $user->id;
                $shop->slug = 'demo-shop-' . $user->id;
                $shop->save();

                flash(translate('Seller has been inserted successfully'))->success();
                return redirect()->route('sellers.index');
            }
        }
        flash(translate('Something went wrong'))->error();
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
        $shop = Shop::findOrFail(decrypt($id));
        return view('backend.sellers.edit', compact('shop'));
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
        $shop = Shop::findOrFail($id);
        $user = $shop->user;
        $user->name = $request->name;
        $user->email = $request->email;
        if (strlen($request->password) > 0) {
            $user->password = Hash::make($request->password);
        }
        if ($user->save()) {
            if ($shop->save()) {

                $body = "Profile Update Alert<br>
                    Your seller information has been updated by our admin team for.<br>
                    Please review the changes and contact support if you have any queries or concerns.";
                sendSellerNotification($user->id, 'seller_update', null, null, null, $body);

                flash(translate('Seller has been updated successfully'))->success();
                return redirect()->route('sellers.index');
            }
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shop = Shop::findOrFail($id);
        Product::where('user_id', $shop->user_id)->delete();
        $orders = Order::where('user_id', $shop->user_id)->get();

        foreach ($orders as $key => $order) {
            OrderDetail::where('order_id', $order->id)->delete();
        }
        Order::where('user_id', $shop->user_id)->delete();

        User::destroy($shop->user->id);

        $notificationData = [
            'name' => $shop->user->name,
            'body' => Config('notification.seller_delete'),
            'thanks' => 'Thank you'
        ];

        try {
            \Notification::send($shop->user, new SellerDelete($notificationData));
        } catch (Exception $e) {
            // dd($e);
            echo "<script>console.log('" . $e . "')</script>";
            // return back();
        }

        if (Shop::destroy($id)) {
            flash(translate('Seller has been deleted successfully'))->success();
            return redirect()->route('sellers.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function bulk_seller_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $shop_id) {
                $this->destroy($shop_id);
            }
        }

        return 1;
    }

    public function show_verification_request($id)
    {
        $shop = Shop::findOrFail($id);
        return view('backend.sellers.verification', compact('shop'));
    }

    public function approve_seller($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->verification_status = 1;
        if ($shop->save()) {
            Cache::forget('verified_sellers_id');
            flash(translate('Seller has been approved successfully'))->success();
            return redirect()->route('sellers.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function reject_seller($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->verification_status = 0;
        $shop->verification_info = null;
        if ($shop->save()) {
            Cache::forget('verified_sellers_id');
            flash(translate('Seller verification request has been rejected successfully'))->success();
            return redirect()->route('sellers.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }


    public function payment_modal(Request $request)
    {
        $shop = shop::findOrFail($request->id);
        return view('backend.sellers.payment_modal', compact('shop'));
    }

    public function profile_modal(Request $request)
    {
        $shop = Shop::findOrFail($request->id);
        $subscription = Subscription::with('plan')->where('user_id', $shop->user_id)->where('status', 'S')->whereDate('valid_upto', '>=', date('Y-m-d'))->orderBy('id', 'desc')->first();

        return view('backend.sellers.profile_modal', compact('shop', 'subscription'));
    }

    public function updateGstStatus(Request $request)
    {
        $shop = Shop::find($request->id);
        if (!is_null($shop)) {
            $shop->gst_number_status = $request->status;
            $shop->save();
            return "success";
        }
        return "failed";
    }

    public function updateApproved(Request $request)
    {
        $shop = Shop::findOrFail($request->id);
        $shop->verification_status = $request->status;
        if ($shop->save()) {
            Cache::forget('verified_sellers_id');

            $seller = User::find($shop->user_id);
            if ($shop->verification_status == 1) {
                $body = "🎉 <b>Shop Approval Celebration!</b> 🎉<br>
                    Good news, " . $seller->name . "! Your shop on " . env('APP_NAME') . " has been reviewed and officially approved.<br>
                    <b>Your Shop Details:</b> <br>
                    &emsp;Shop Name: " . $shop->name . ",<br>
                    &emsp;Date of Approval: " . date('d M, Y') . "<br>
                    Make the most of this opportunity and set the stage for success!";
                sendSellerNotification($shop->user_id, 'shop_approval', null, null, null, $body);

                //Mail
                $array['view'] = 'emails.quotationReplyMail';
                $array['subject'] = translate("Congratulations! Your Shop on " . env('APP_NAME') . " is Approved!");
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['link'] = url('/');
                $array['content'] = "
                  Dear " . $seller->name . ",<br>
                  <br>
                  We are thrilled to inform you that your shop application on " . env('APP_NAME') . " has been reviewed and approved!<br>
                  Welcome to our vibrant marketplace, where thousands of customers are eager to discover your products. <br>
                  <br>
                  Details of Your Shop:<br>
                  Shop Name: " . $shop->name . "<br>
                  Registration Date: " . $shop->created_at . "<br>
                  🔗<a href=" . url('/') . ">Timber buddy</a><br>
                  <br>
                  Need Assistance?<br>
                  Our dedicated seller support team is here to help guide you as you set up and run your shop. If you have any questions or need assistance, don't hesitate to contact us at " . Auth::user()->email . " or explore our comprehensive FAQ and resources section.
                  Thank you for choosing Timber Buddy as your selling platform. We are committed to providing you with the best tools and support to succeed in this marketplace. Here's to a prosperous journey ahead!<br>
                  <br>
                  Warm regards,<br>
                  " . env('APP_NAME') . "<br>
                  " . Auth::user()->phone ?? '' . "";
                $array['title'] = "Congratulations! Your Shop on " . env('APP_NAME') . " is Approved!";
                Mail::to($seller->email)->send(new EmailManager($array));
            } else {
                $body = "🚫 Shop Application Status on " . env('APP_NAME') . " 🚫<br>
                    Dear " . $shop->name . ", Your shop on " . env('APP_NAME') . " has been rejected.<br>
                    If you have questions or need further clarification, please reach out to our support team via the app.<br>
                    Thank you for your understanding and interest in " . env('APP_NAME') . ".";
                sendSellerNotification($shop->user_id, 'shop_disapproval', null, null, null, $body);

                //Mail
                $array['view'] = 'emails.quotationReplyMail';
                $array['subject'] = translate("Your Shop on " . env('APP_NAME') . " is Non-Verified!");
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['link'] = url('/');
                $array['content'] = "
                  Dear " . $seller->name . ",<br>
                  <br>
                  This email is to inform you that after careful review, your shop on " . env('APP_NAME') . " has been Non-Verified!.<br>
                (Reasons for Rejection if rejected, brief explanation)<br>
                <br>
                If you have further questions or require clarification, please contact our support team at [support@email.com]<br>
                <br>
                  Warm regards,<br>
                  " . env('APP_NAME') . "";
                $array['title'] = "Your Shop on " . env('APP_NAME') . " is Non-Verified!";
                Mail::to($seller->email)->send(new EmailManager($array));
            }

            return 1;
        }
        return 0;
    }

    public function login($id)
    {
        $shop = Shop::findOrFail(decrypt($id));
        $user  = $shop->user;
        auth()->login($user, true);

        return redirect()->route('seller.dashboard');
    }

    public function ban($id)
    {
        $shop = Shop::findOrFail($id);

        if ($shop->user->banned == 1) {
            $shop->user->banned = 0;
            flash(translate('Seller has been unbanned successfully'))->success();
        } else {
            $shop->user->banned = 1;
            flash(translate('Seller has been banned successfully'))->success();

            $body = "Dear " . $shop->user->name . ",
                Your selling privileges have been suspended.<br>
                Please review our guidelines and contact support for more details or to appeal. <br>
                -" . env('APP_NAME') . " Team";
            sendSellerNotification($shop->user->id, 'seller_ban', null, null, null, $body);
        }

        $shop->user->save();
        return back();
    }
}
