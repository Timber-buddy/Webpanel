<?php

namespace App\Http\Controllers\Seller;

use App\Models\Subscription;
use Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Subscription::with('plan')->where('user_id', getSellerId())->orderBy('id', 'desc')->paginate(10);
        return view('seller.payment_history', compact('payments'));
    }
}
