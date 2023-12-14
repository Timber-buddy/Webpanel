<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ProductQuery;
use App\Models\Product;
use Auth;
use Illuminate\Http\Request;

class ProductQueryController extends Controller
{
    /**
     * Retrieve queries that belongs to current seller
     */
    public function index()
    {
        $queries = ProductQuery::where('seller_id', getSellerId())->latest()->paginate(20);
        return view('seller.product_query.index', compact('queries'));
    }
    /**
     * Retrieve specific query using query id.
     */
    public function show($id)
    {
        $query = ProductQuery::find(decrypt($id));
        return view('seller.product_query.show', compact('query'));
    }

    public function edit($id)
    {
        $query = ProductQuery::find(decrypt($id));
        return view('seller.product_query.edit', compact('query'));
    }

    public function delete($id)
    {
        $query = ProductQuery::find(decrypt($id));
        $query->delete();
        flash(translate('Query has been removed successfully'))->success();
        return redirect()->back();
    }
    
    /**
     * Store reply against the question from seller panel
     */

    public function reply(Request $request, $id)
    {
        $this->validate($request, [
            'reply' => 'required',
        ]);
        $query = ProductQuery::find($id);
        $query->reply = $request->reply;
        $query->save();

        $product = Product::find($query->product_id);
        
        $body = "💬 New Message Received!<br>
            You have a response from ".Auth::user()->name." regarding your conversation on ".$product->name.".
            Tap to read the message.";
        sendNotification($query->customer_id, "product_enquiry", $product->slug, null, null, $body);

        flash(translate('Replied successfully!'))->success();
        return redirect()->route('seller.product_query.index');
    }
}
