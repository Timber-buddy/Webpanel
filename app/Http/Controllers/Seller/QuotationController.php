<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Mail\SellerQuotationReplyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\User;
use App\Models\QuotationMessage;
use App\Models\Product;
use Auth;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $quotations = Quotation::where('seller_id',getSellerId())->orderBy('id', 'desc')->get();
        return view('seller.quotations.index',compact('quotations'));
    }

    public function view(Request $request, $id = null)
    {
        $quotation = Quotation::with('attributes', 'attributes_data')
            ->find(decrypt($request->id));
        return view('seller.quotations.view',compact('quotation'));
    }

    public function mail(Request $request)
    {
        $user =User::where('name',$request->customer_name)->first();
        $quotation = new QuotationMessage();
        $quotation->quotation_id = $request->quotation_id;
        $quotation->user_id = getSellerId();
        $quotation->message = $request->message;
        $quotation->status = !empty($request->status) ? $request->status : '0';
        $quotation->save();

        $array['view'] = 'emails.quotationReplyMail';
        $array['subject'] = translate('Timber Buddy');
        $array['from'] = Auth::user()->email;
        $array['content'] = 'Reply : '. $request->message;
        $array['title'] = 'Timber Buddy ' . Auth::user()->name . ' Quotation Reply Mail';

        Mail::to($user->email)->send(new SellerQuotationReplyMail($array));

        $quotation = Quotation::find($request->quotation_id);
        $product = Product::find($quotation->product_id);

        $body = "🔖 Quotation Response Received!<br>
            Your request for ".$product->name." has been addressed.<br>
            Tap to view the detailed response.";

        sendNotification($user->id, "quotation_reply", null, $request->quotation_id, null, $body);

        return back();
    }

    public function destroy($id)
    {
        $quotation = Quotation::find($id);

        if (!$quotation) {
            abort(404, 'Quotation not found');
        }

        $quotation->attributes()->delete();
        $quotation->attributes_data()->delete();
        $quotation->delete();

        flash(translate('Quotation deleted successfully'))->success();
            return redirect()->route('seller.quotation.all');
    }
}
