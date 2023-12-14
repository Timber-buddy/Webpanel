<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductQuery;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class ProductQueryController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_product_queries'])->only('admin_index');
    }

    /**
     * Retrieve queries that belongs to current seller
     */
    public function index()
    {
        $admin_id = User::where('user_type','admin')->first()->id;
        $queries = ProductQuery::where('seller_id', $admin_id)->latest()->paginate(20);
        return view('backend.support.product_query.index', compact('queries'));
    }

    /**
     * Retrieve specific query using query id.
     */
    public function show($id)
    {
        $query = ProductQuery::find(decrypt($id));
        return view('backend.support.product_query.show', compact('query'));
    }

    public function edit($id)
    {
        $query = ProductQuery::find(decrypt($id));
        return view('backend.support.product_query.edit', compact('query'));
    }

    public function delete($id)
    {
        $query = ProductQuery::find(decrypt($id));
        $query->delete();
        flash(translate('Query has been removed successfully'))->success();
        return redirect()->back();
    }

    /**
     * store products queries through the ProductQuery model
     * data comes from product details page
     * authenticated user can leave queries about the product
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|string',
        ]);
        $product = Product::find($request->product);

        $query = new ProductQuery();
        $query->customer_id = Auth::id();
        $query->seller_id = $product->user_id;
        $query->product_id = $product->id;
        $query->question = $request->question;
        $query->save();

        $user = User::findOrFail($product->user_id);
        if ($user->user_type == "seller") 
        {
            $body = "🔔 New Product Inquiry!<br>
                A customer has questions about ".$product->name.". Tap to view and respond.";
            sendSellerNotification($product->user_id, 'seller_product_enquiry', null, $query->id, null, $body);
        }
        else
        {
            $seller = User::find($product->user_id);
            $body = "🛍️ Product Inquiry Alert!<br>
                Customer inquiry for ".$product->name." by ".$seller->name.". Tap to review details.";
            sendAdminNotification($product->user_id, 'admin_product_enquiry', null, $query->id, null, $body);
        }

        flash(translate('Your query has been submittes successfully'))->success();
        return redirect()->back();
    }

    /**
     * Store reply against the question from Admin panel
     */

    public function reply(Request $request, $id)
    {
        $this->validate($request, [
            'reply' => 'required',
        ]);
        $query = ProductQuery::find($id);
        $query->reply = $request->reply;
        $query->save();
        flash(translate('Replied successfully!'))->success();

        $product = Product::find($query->product_id);

        $body = "✉️ Message Sent Successfully!<br>
                Your reply to ".$product->user->name."'s conversation about ".$product->name." has been dispatched.<br>
                Tap to view or manage the conversation.";
        sendNotification($query->customer_id, "product_enquiry", $product->slug, null, null, $body);

        return redirect()->route('product_query.index');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'question' => 'required',
        ]);
        $query = ProductQuery::find(decrypt($id));
        $query->question = $request->question;
        $query->save();
        flash(translate('Updated successfully!'))->success();
        return redirect()->back();
    }
}
