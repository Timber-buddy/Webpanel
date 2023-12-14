<?php

namespace App\Http\Controllers;

use App\Models\FollowSeller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SellerFollow;


class FollowSellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $followed_sellers = FollowSeller::where('user_id', Auth::user()->id)->orderBy('shop_id', 'asc')->paginate(10);
        return view('frontend.user.customer.followed_sellers', compact('followed_sellers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(isCustomer())
        {
            $followed_seller = FollowSeller::where('user_id', Auth::user()->id)->where('shop_id', $request->id)->first();
            if($followed_seller == null){
                FollowSeller::insert([
                    'user_id' => Auth::user()->id,
                    'shop_id' => $request->id
                ]);

                $shop = Shop::findOrFail($request->id);

                $notificationData = [
                    'name' => Auth::user()->name,
                    'body' => Config('notification.seller_followed'),
                    'thanks' => 'Thank you',
                    'slug' => $shop->slug,
                ];

                try {
                    \Notification::send(Auth::user(), new SellerFollow($notificationData));
                } catch (Exception $e) {
                    // dd($e);
                    echo "<script>console.log('".$e."')</script>";
                    // return back();
                }

                $seller = User::find($shop->user_id);

                $body = "🌟 <b>New Follower Alert!</b> 🌟<br>
                    Great news, ".$seller->name."! A customer has just started following your store on ".env('APP_NAME')."<br>
                    <b>Follower Details:</b> <br>
                    &emsp;1)Username: ".Auth::user()->name." <br>
                    &emsp;2)Joined Date: ".date('d M, Y', strtotime(Auth::user()->created_at));

                sendSellerNotification($shop->user_id, 'new_follower', null, null, null, $body);
            }

            flash(translate('Seller is followed Successfully'))->success();
            return back();
        }
        flash(translate('You need to login as a customer to follow this seller'))->success();
        return back();
    }

    public function remove(Request $request)
    {
        $followed_seller = FollowSeller::where('user_id', Auth::user()->id)->where('shop_id', $request->id)->first();
        if($followed_seller!=null){
            FollowSeller::where('user_id', Auth::user()->id)->where('shop_id', $request->id)->delete();
            flash(translate('Seller is unfollowed Successfully'))->success();
            return back();
        }
    }
}
