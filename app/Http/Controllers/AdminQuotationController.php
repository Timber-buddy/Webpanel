<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\SellerQuotationReplyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\User;
use App\Models\QuotationMessage;
use App\Models\Product;
use Auth;

class AdminQuotationController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->user_type == "admin")
        {
            $quotations = Quotation::where('seller_id', Auth::user()->id)->orderBy('id', 'desc')->get();
        }
        else
        {
            $admin = User::where('user_type', 'admin')->first();
            $quotations = Quotation::where('seller_id', $admin->id)->orderBy('id', 'desc')->get();
        }

        return view('backend.quotations.index',compact('quotations'));
    }

    public function view($id)
    {
        $quotation = Quotation::with('attributes', 'attributes_data')->find(decrypt($id));

        return view('backend.quotations.view',compact('quotation'));
    }

    public function mail(Request $request)
    {
        $user =User::where('id',$request->customer_id)->first();

        $quoteMessage = new QuotationMessage();
        $quoteMessage->quotation_id = $request->quotation_id;
        $quoteMessage->user_id = Auth::user()->id;
        $quoteMessage->message = $request->message;
        $quoteMessage->status = !empty($request->status) ? $request->status : '0';
        $quoteMessage->save();

        $array['view'] = 'emails.quotationReplyMail';
        $array['subject'] = translate('Timber Buddy');
        $array['from'] = Auth::user()->email;
        $array['content'] = 'Reply : '. $request->message;
        $array['title'] = 'Timber Buddy ' . Auth::user()->name . ' Quotation Reply Mail';

        // Mail::to($user->email)->send(new SellerQuotationReplyMail($array));

        $quotation = Quotation::find($request->quotation_id);
        $product = Product::find($quotation->product_id);

        $body = "🔖 Quotation Response Received!<br>
            Your request for ".$product->name." has been addressed.<br>
            Tap to view the detailed response.";

        sendNotification($user->id, "quotation_reply", null, $request->quotation_id, null, $body);

        return redirect(url('/admin/quotation/view/'. encrypt($request->quotation_id)));
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

        // return redirect(url('/admin/quotation/all'))->with('success', 'Quotation deleted successfully');
        // flash(translate('Quotation deleted successfully'))->success();
        // return back();

        flash(translate('Quotation deleted successfully'))->success();
            return redirect()->route('quotation.all');
    }
}
