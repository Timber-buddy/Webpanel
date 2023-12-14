<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\User;
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
        return back();
    }
}
