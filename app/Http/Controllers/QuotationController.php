<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Http\Request;
use App\Models\QuotationMessage;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::where('customer_id', Auth::user()->id)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate(10);
        return view('frontend.user.quotation.index',compact('quotations'));
    }

    public function show(Request $request)
    {
        $quotation = Quotation::with(['attributes', 'attributes_data'])->find($request->id);
        $user = User::where('id', $quotation->seller_id)->pluck('name');
        $messages = QuotationMessage::where('quotation_id', $quotation->id)->get();
        return view('frontend.user.quotation.show', compact('quotation','user','messages'));
    }

    public function message(Request $request)
    {
        $quotation = new QuotationMessage();
        $quotation->quotation_id = $request->quotation_id;
        $quotation->user_id = Auth::user()->id;
        $quotation->message = $request->message;
        $quotation->status = !empty($request->status) ? $request->status : '0';
        $quotation->save();

        $quotation = Quotation::find($request->quotation_id);
        $product = Product::find($quotation->product_id);

        $body = "🔖 Quotation Response Received!<br>
            Your request for ".$product->name." has been addressed.<br>
            Tap to view the detailed response.";

        if($product->user->user_type == 'seller'){
            sendSellerNotification($product->user_id, "quotation_reply", null, $request->quotation_id, null, $body);
        }else{
            sendAdminNotification($product->user_id, "quotation_reply", null, $request->quotation_id, null, $body);
        }
        return back();
    }
}
