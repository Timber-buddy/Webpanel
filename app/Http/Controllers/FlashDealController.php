<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\FlashDeal;
use App\Mail\EmailManager;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Session;
use App\Models\FlashDealProduct;
use App\Models\FlashDealTranslation;
use Illuminate\Support\Facades\Mail;

class FlashDealController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_flash_deals'])->only('index');
        $this->middleware(['permission:add_flash_deal'])->only('create');
        $this->middleware(['permission:edit_flash_deal'])->only('edit');
        $this->middleware(['permission:delete_flash_deal'])->only('destroy');
        $this->middleware(['permission:publish_flash_deal'])->only('update_featured');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $flash_deals = FlashDeal::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $flash_deals = $flash_deals->where('title', 'like', '%'.$sort_search.'%');
        }
        $flash_deals = $flash_deals->paginate(15);
        return view('backend.marketing.flash_deals.index', compact('flash_deals', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.marketing.flash_deals.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $flash_deal = new FlashDeal;
        $flash_deal->title = $request->title;
        $flash_deal->text_color = $request->text_color;

        $date_var               = explode(" to ", $request->date_range);
        $flash_deal->start_date = strtotime($date_var[0]);
        $flash_deal->end_date   = strtotime( $date_var[1]);

        $flash_deal->background_color = $request->background_color;
        $flash_deal->slug = Str::slug($request->title).'-'.Str::random(5);
        $flash_deal->banner = $request->banner;
        if($flash_deal->save()){
            foreach ($request->products as $key => $product) {
                $flash_deal_product = new FlashDealProduct;
                $flash_deal_product->flash_deal_id = $flash_deal->id;
                $flash_deal_product->product_id = $product;
                $flash_deal_product->save();

                $root_product = Product::findOrFail($product);
                $root_product->discount = $request['discount_'.$product];
                $root_product->discount_type = $request['discount_type_'.$product];
                $root_product->discount_start_date = strtotime($date_var[0]);
                $root_product->discount_end_date   = strtotime( $date_var[1]);
                $root_product->save();

                $seller = User::find($root_product->user_id);
                $body = "🎉 Exciting News! Your Product is Featured in Our Flash Sale! 🎉<br>
                    Hello ".$seller->name.",<br>
                    We're thrilled to inform you that one or more of your products have been selected for our upcoming Flash Sale on ".env('APP_NAME')."!<br>
                    Let's make the most of this opportunity! For any assistance or queries, reach out to our seller support within the app.";

                sendSellerNotification($root_product->user_id, 'seller_flash_deal', $root_product->slug, $root_product->id, null, $body);
                if($seller->user_type == 'seller'){
                   //Mail
                $array['view'] = 'emails.quotationReplyMail';
                $array['subject'] = translate("⚡ Flash Sale Alert! Limited-Time Deals Just For You! ⚡");
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['link'] = url('/');
                $array['content'] = "
                Dear " . $seller->name . ",<br>
                <br>
                    One (or more) of your products has been handpicked to be featured in our upcoming Flash Sale on ".env('APP_NAME').".<br>
                    This is a great opportunity to boost your visibility and sales! <br>
                    <br>
                    Flash Sale Details: 1)Start Date & Time: ".$root_product->discount_start_date." 2)End Date & Time:".$root_product->discount_end_date." 3)Discount: Up to ".$root_product->discount."/-".$root_product->discount_type." off on selected items! <br>
                    We believe this Flash Sale will be a tremendous success for all involved, and we're excited to see your product in the spotlight.<br>
                    <br>
                    Thank you for being a valued member of ".env('APP_NAME').". <br>
                    If you have any questions or need further information, don't hesitate to contact our seller support team at [support@email.com].<br>
                <br>
                Warm regards,<br>
                " . env('APP_NAME') . "";
                $array['title'] = "⚡ Flash Sale Alert! Limited-Time Deals Just For You! ⚡";
                Mail::to($seller->email)->send(new EmailManager($array));
                }

            }

            // $flash_deal_translation = FlashDealTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'flash_deal_id' => $flash_deal->id]);
            // $flash_deal_translation->title = $request->title;
            // $flash_deal_translation->save();

            Session::put(['message' => 'Flash Deal has been inserted successfully', 'SmgStatus' => 'success']);
            return redirect()->route('flash_deals.index');
        }
        else{
            Session::put(['message' => 'Something went wrong', 'SmgStatus' => 'danger']);
            return back();
        }
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
    public function edit(Request $request, $id)
    {
        $lang           = $request->lang;
        $flash_deal = FlashDeal::findOrFail($id);
        return view('backend.marketing.flash_deals.edit', compact('flash_deal','lang'));
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
        $flash_deal = FlashDeal::findOrFail($id);

        $flash_deal->text_color = $request->text_color;

        $date_var               = explode(" to ", $request->date_range);
        $flash_deal->start_date = strtotime($date_var[0]);
        $flash_deal->end_date   = strtotime( $date_var[1]);

        $flash_deal->background_color = $request->background_color;

        if($request->lang == env("DEFAULT_LANGUAGE")){
          $flash_deal->title = $request->title;
          if (($flash_deal->slug == null) || ($flash_deal->title != $request->title)) {
              $flash_deal->slug = strtolower(str_replace(' ', '-', $request->title) . '-' . Str::random(5));
          }
        }

        $flash_deal->banner = $request->banner;
        foreach ($flash_deal->flash_deal_products as $key => $flash_deal_product) {
            $flash_deal_product->delete();
        }
        if($flash_deal->save()){
            foreach ($request->products as $key => $product) {
                $flash_deal_product = new FlashDealProduct;
                $flash_deal_product->flash_deal_id = $flash_deal->id;
                $flash_deal_product->product_id = $product;
                $flash_deal_product->save();

                $root_product = Product::findOrFail($product);
                $root_product->discount = $request['discount_'.$product];
                $root_product->discount_type = $request['discount_type_'.$product];
                $root_product->discount_start_date = strtotime($date_var[0]);
                $root_product->discount_end_date   = strtotime( $date_var[1]);
                $root_product->save();
            }

            $sub_category_translation = FlashDealTranslation::firstOrNew(['lang' => $request->lang, 'flash_deal_id' => $flash_deal->id]);
            $sub_category_translation->title = $request->title;
            $sub_category_translation->save();

            flash(translate('Flash Deal has been updated successfully'))->success();
            return back();
        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $flash_deal = FlashDeal::findOrFail($id);
        $flash_deal->flash_deal_products()->delete();
        $flash_deal->flash_deal_translations()->delete();

        FlashDeal::destroy($id);
        Session::put(['message' => 'FlashDeal has been deleted successfully', 'SmgStatus' => 'success']);
        return redirect()->route('flash_deals.index');
    }

    public function update_status(Request $request)
    {
        $flash_deal = FlashDeal::findOrFail($request->id);
        $flash_deal->status = $request->status;
        if($flash_deal->save()){
            flash(translate('Flash deal status updated successfully'))->success();

            if($flash_deal->status == 1)
            {
                $users = User::where('user_type', 'customer')->where('banned', 0)->get();
                if(!is_null($users))
                {
                    foreach($users as $user)
                    {
                        $body = "🚀 Flash Sale is LIVE! Grab Exclusive Deals Now! 🚀<br>
                            Hey ".$user->name.", The wait is over! Dive into our exclusive Flash Sale on ".env('APP_NAME')." and snag some amazing deals before they disappear.<br>
                            Key Highlights: <br>
                            1)Limited-time offers on your favorite brands. <br>
                            2)Exclusive items available only for this sale.<br>
                            From ".date('d M, Y h:i a', $flash_deal->start_date)." to ".date('d M, Y h:i a', $flash_deal->end_date).". Don't miss out!";

                        sendNotification($user->id, 'flash_sale', $flash_deal->slug, $flash_deal->id, null, $body);


                    //Send to Mail for All Customers
                        //  $array['view'] = 'emails.quotationReplyMail';
                        //  $array['subject'] = translate("⚡ Flash Sale Alert! Limited-Time Deals Just For You! ⚡");
                        //  $array['from'] = env('MAIL_FROM_ADDRESS');
                        //  $array['link'] = url('/');
                        //  $array['content'] = "
                        //  Dear " . $user->name . ",<br>
                        //  <br>
                        //  Exciting news from ".env('APP_NAME')."! We're having a FLASH SALE, and you're invited to grab the best deals before they vanish.<br>
                        //  Flash Sale Details: 1)Start Date & Time: ".date('d M, Y h:i a', $flash_deal->start_date)." 2)End Date & Time: ".date('d M, Y h:i a', $flash_deal->end_date)." 3)Discount: Up to ".$flash_deal->discount."/-".$flash_deal->discount_type." off on selected items!<br>
                        //  <br>
                        //  If you have any questions or need assistance navigating the sale, our support team is here to help. Reach out to us at [support@email.com] or check our FAQ section.<br>
                        //  Happy shopping and enjoy the savings!.<br>
                        //  <br>
                        //  Warm regards,<br>
                        //  " . env('APP_NAME') . "";
                        //  $array['title'] = "⚡ Flash Sale Alert! Limited-Time Deals Just For You! ⚡";
                        //  Mail::to($user->email)->send(new EmailManager($array));

                    }
                }
            }
            Session::put(['message' => 'Flash deal status updated successfully', 'SmgStatus' => 'success']);
            return 1;
        }
        return 0;
    }

    public function update_featured(Request $request)
    {
        foreach (FlashDeal::all() as $key => $flash_deal) {
            $flash_deal->featured = 0;
            $flash_deal->save();
        }
        $flash_deal = FlashDeal::findOrFail($request->id);
        $flash_deal->featured = $request->featured;
        if($flash_deal->save()){
            flash(translate('Flash deal status updated successfully'))->success();
            return 1;
        }
        return 0;
    }

    public function product_discount(Request $request){
        $product_ids = $request->product_ids;
        return view('backend.marketing.flash_deals.flash_deal_discount', compact('product_ids'));
    }

    public function product_discount_edit(Request $request){
        $product_ids = $request->product_ids;
        $flash_deal_id = $request->flash_deal_id;
        return view('backend.marketing.flash_deals.flash_deal_discount_edit', compact('product_ids', 'flash_deal_id'));
    }
}
