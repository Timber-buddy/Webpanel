<?php

namespace App\Http\Controllers;

use Str;
use Cache;
use Artisan;
use Session;
use Combinations;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\Quotation;
use App\Mail\EmailManager;
use App\Models\ProductTax;
use App\Models\FollowSeller;
use CoreComponentRepository;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\QuotationMessage;
use App\Services\ProductService;
use App\Models\ProductTranslation;
use App\Models\QuotationAttribute;
use Illuminate\Support\Facades\DB;
use App\Notifications\SellerUpdate;
use App\Services\ProductTaxService;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ProductRequest;
use App\Services\ProductStockService;


use App\Models\QuotationAttributeData;
use App\Services\ProductFlashDealService;
use Illuminate\Support\Facades\Validator;
use AizPackages\CombinationGenerate\Services\CombinationService;

class ProductController extends Controller
{
    protected $productService;
    protected $productTaxService;
    protected $productFlashDealService;
    protected $productStockService;

    public function __construct(
        ProductService $productService,
        ProductTaxService $productTaxService,
        ProductFlashDealService $productFlashDealService,
        ProductStockService $productStockService
    ) {
        $this->productService = $productService;
        $this->productTaxService = $productTaxService;
        $this->productFlashDealService = $productFlashDealService;
        $this->productStockService = $productStockService;

        // Staff Permission Check
        $this->middleware(['permission:add_new_product'])->only('create');
        $this->middleware(['permission:show_all_products'])->only('all_products');
        $this->middleware(['permission:show_in_house_products'])->only('admin_products');
        $this->middleware(['permission:show_seller_products'])->only('seller_products');
        $this->middleware(['permission:product_edit'])->only('admin_product_edit', 'seller_product_edit');
        $this->middleware(['permission:product_duplicate'])->only('duplicate');
        $this->middleware(['permission:product_delete'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_products(Request $request)
    {
        CoreComponentRepository::instantiateShopRepository();

        $type = 'In House';
        $col_name = null;
        $query = null;
        $sort_search = null;

        $products = Product::where('added_by', 'admin')->where('auction_product', 0)->where('wholesale_product', 0);

        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products
                ->where('name', 'like', '%' . $sort_search . '%')
                ->orWhereHas('stocks', function ($q) use ($sort_search) {
                    $q->where('sku', 'like', '%' . $sort_search . '%');
                });
        }

        $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'sort_search'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function seller_products(Request $request)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::where('added_by', 'seller')->where('auction_product', 0)->where('wholesale_product', 0);
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);
        $type = 'Seller';

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }

    public function all_products(Request $request)
    {
        // dd(Session::get('testsession'));
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::orderBy('created_at', 'desc')->where('auction_product', 0)->where('wholesale_product', 0);
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products
                ->where('name', 'like', '%' . $sort_search . '%')
                ->orWhereHas('stocks', function ($q) use ($sort_search) {
                    $q->where('sku', 'like', '%' . $sort_search . '%');
                });
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        $products = $products->paginate(15);
        $type = 'All';

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // CoreComponentRepository::initializeCache();

        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.products.create', compact('categories'));
    }

    public function add_more_choice_option(Request $request)
    {
        $all_attribute_values = AttributeValue::with('attribute')->where('attribute_id', $request->attribute_id)->get();

        $html = '';

        foreach ($all_attribute_values as $row) {
            $html .= '<option value="' . $row->value . '">' . $row->value . '</option>';
        }

        echo json_encode($html);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => 'required',
            'unit' => 'required',
            'min_qty' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'current_stock' => 'required|numeric',
            'photos' => 'required',
            'thumbnail_img' => 'required',
            'description' => 'required'
        ]);
        if ($request->get('discount_type') == 'amount') {
            $validator->addRules([
                'discount' => 'required|numeric|lt:unit_price',
            ]);
        } else {
            $validator->addRules([
                'discount' => 'required|numeric|lt:100',
            ]);
        }
        if ($request->get('choice_attributes') != null) {
            $validator->addRules([
                'choice' => 'required',
            ]);
        }
        // if ($validator->fails()) {
        //     return back()
        //         ->withErrors($validator)
        //         ->withInput()
        //         ->with('error', 'Please Check the Form.');
        // }


        if ($validator->fails()) {
            $errorMessage = $validator->errors()->all();
            return redirect()->route('products.create', compact('errorMessage'));
        }

        try {
            DB::beginTransaction();

            $product = $this->productService->store($request->except([
                '_token', 'sku', 'choice', 'tax_id', 'tax', 'tax_type', 'flash_deal_id', 'flash_discount', 'flash_discount_type'
            ]));
            $request->merge(['product_id' => $product->id]);

            //VAT & Tax
            if ($request->tax_id) {
                $this->productTaxService->store($request->only([
                    'tax_id', 'tax', 'tax_type', 'product_id'
                ]));
            }

            //Flash Deal
            $this->productFlashDealService->store($request->only([
                'flash_deal_id', 'flash_discount', 'flash_discount_type'
            ]), $product);

            //Product Stock
            $this->productStockService->store($request->only([
                'colors_active', 'colors', 'choice_no', 'unit_price', 'sku', 'current_stock', 'product_id'
            ]), $product);

            // Product Translations
            $request->merge(['lang' => 'en']);
            ProductTranslation::create($request->only([
                'lang', 'name', 'unit', 'description', 'product_id'
            ]));


            Artisan::call('view:clear');
            Artisan::call('cache:clear');

             DB::commit();
            Session::put(['message' => 'Product has been inserted successfully', 'SmgStatus' => 'success']);
            return redirect()->route('products.admin');

        } catch (\Exception $e) {
            DB::rollBack();
            Session::put(['message' => 'Something Wrong', 'SmgStatus' => 'danger']);
            return redirect()->back();
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
    public function admin_product_edit(Request $request, $id)
    {
        CoreComponentRepository::initializeCache();

        $product = Product::findOrFail($id);
        if ($product->digital == 1) {
            return redirect('admin/digitalproducts/' . $id . '/edit');
        }

        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function seller_product_edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if ($product->digital == 1) {
            return redirect('digitalproducts/' . $id . '/edit');
        }
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        // $categories = Category::all();
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();

            $product = $this->productService->update($request->except([
                '_token', 'sku', 'choice', 'tax_id', 'tax', 'tax_type', 'flash_deal_id', 'flash_discount', 'flash_discount_type'
            ]), $product);

            //Product Stock
            foreach ($product->stocks as $key => $stock) {
                $stock->delete();
            }

            $request->merge(['product_id' => $product->id]);
            $this->productStockService->store($request->only([
                'colors_active', 'colors', 'choice_no', 'unit_price', 'sku', 'current_stock', 'product_id'
            ]), $product);

            //Flash Deal
            $this->productFlashDealService->store($request->only([
                'flash_deal_id', 'flash_discount', 'flash_discount_type'
            ]), $product);

            //VAT & Tax
            if ($request->tax_id) {
                ProductTax::where('product_id', $product->id)->delete();
                $this->productTaxService->store($request->only([
                    'tax_id', 'tax', 'tax_type', 'product_id'
                ]));
            }
            //Product

            // Product Translations
            // ProductTranslation::updateOrCreate(
            //     $request->only([
            //         'lang', 'product_id'
            //     ]),
            //     $request->only([
            //         'name', 'unit', 'description'
            //     ])
            // );

            if ($product->user->user_type == "seller") {
                $body = "Product Update Alert<br>
                Your product " . $product->name . " has been modified by our admin team. <br>
                Check the changes and contact support if you have any concerns.";
                sendSellerNotification($product->user->id, 'seller_product_update', null, null, null, $body);
            }

            DB::commit();
            Session::put(['message' => 'Product has been updated successfully', 'SmgStatus' => 'success']);
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::put(['message' => 'Something Wrong', 'SmgStatus' => 'danger']);
            return redirect()->back();
            // Handle the exception
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
        $product = Product::findOrFail($id);

        $product->product_translations()->delete();
        $product->stocks()->delete();
        $product->taxes()->delete();

        $quote = Quotation::where('product_id', $id)->first();
        if (!is_null($quote)) {
            QuotationAttributeData::where('quotaton_id', $quote->id)->delete();
            QuotationAttribute::where('quotation_id', $quote->id)->delete();
            QuotationMessage::where('quotation_id', $quote->id)->delete();
            $quote->delete();
        }

        if (Product::destroy($id)) {
            $carts = Cart::where('product_id', $id)->get();
            if (!is_null($carts)) {
                foreach ($carts as $cart) {
                    sendNotification($cart->user_id, "cart_item_deleted");
                }

                Cart::where('product_id', $id)->delete();
            }

            $wishlists = Wishlist::where('product_id', $id)->get();
            if (!is_null($wishlists)) {
                foreach ($wishlists as $wishlist) {
                    sendNotification($wishlist->user_id, "wishlist_item_deleted");
                }

                Wishlist::where('product_id', $id)->delete();
            }

            if ($product->added_by == 'seller') {
                $body = "Product Removal Notification.<br>
                    Your product titled " . $product->name . " has been removed. <br>
                    Review our guidelines or contact support for further information.";
                sendSellerNotification($product->user_id, 'product_deleted', $product->slug, $product->id, null, $body);
            }

            flash(translate('Product has been deleted successfully'))->success();

            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            return back();
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function bulk_product_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $product_id) {
                $this->destroy($product_id);
            }
        }

        return 1;
    }

    /**
     * Duplicates the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $product = Product::find($id);

        $product_new = $product->replicate();
        $product_new->slug = $product_new->slug . '-' . Str::random(5);
        $product_new->save();

        //Product Stock
        $this->productStockService->product_duplicate_store($product->stocks, $product_new);

        //VAT & Tax
        $this->productTaxService->product_duplicate_store($product->taxes, $product_new);

        flash(translate('Product has been duplicated successfully'))->success();
        if ($request->type == 'In House')
            return redirect()->route('products.admin');
        elseif ($request->type == 'Seller')
            return redirect()->route('products.seller');
        elseif ($request->type == 'All')
            return redirect()->route('products.all');
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)->get();
        return view('partials.product_select', compact('products'));
    }

    public function updateTodaysDeal(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->todays_deal = $request->status;
        $product->save();
        Cache::forget('todays_deal_products');
        return 1;
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;

        if ($product->added_by == 'seller' && addon_is_activated('seller_subscription') && $request->status == 1) {
            $shop = $product->user->shop;
            if (
                $shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($shop->package_invalid_at), false) < 0
                || $shop->product_upload_limit <= $shop->user->products()->where('published', 1)->count()
            ) {
                return 0;
            }
        }

        $product->save();

        if ($product->added_by == 'seller') {
            if ($request->status == 1) {
                $body = "Product Approval<br>
                    Good news! Your product " . $product->name . " has been approved by the admin. You can now start selling on our platform.";

                sendSellerNotification($product->user_id, 'product_published', $product->slug, $product->id, null, $body);

                // Send to Mail for Seller
                $seller = User::where('id', $product->user_id)->first();
                $array['view'] = 'emails.quotationReplyMail';
                $array['subject'] = translate("Your Product Submission Has Been Published!");
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['link'] = url('/');
                $array['content'] = "
                Dear " . $seller->name . ",<br>
                <br>
                 We are pleased to inform you that your product, " . $product->name . ", has been reviewed and published by our administration team.<br>
                Product Details:<br>
                1) Name: " . $product->name . ",<br>
                2) Description: " . $product->description . ", <br>
                3) Submission Date: " . $product->created_at . " Starting " . $product->updated_at . ",<br>
                Your product will be live and available for purchase on our platform.
                Should you have any questions or require further information, please don't hesitate to reach out to our support team at " . get_setting('contact_email') . " or call us at ".get_setting('contact_phone').".
                Thank you for choosing <a href=" . url('/') . ">Timber buddy</a> as your selling platform. We look forward to seeing your product thrive in our marketplace!<br>
                <br>
                Warm regards,<br>
                " . env('APP_NAME') . "";
                $array['title'] = "Your Product Submission Has Been Published!";
                Mail::to($seller->email)->send(new EmailManager($array));
            } else {
                $body = "Product Status Update<br>
                    Your product " . $product->name . " has been temporarily unpublished.<br>
                    Please review our guidelines and reach out to support for more details.";

                sendSellerNotification($product->user_id, 'product_unpublished', $product->slug, $product->id, null, $body);
            }
        }

        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        return 1;
    }

    public function updateProductApproval(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $product->approved = $request->approved;

        if ($product->added_by == 'seller' && addon_is_activated('seller_subscription')) {
            $shop = $product->user->shop;
            if (
                $shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($shop->package_invalid_at), false) < 0
                || $shop->product_upload_limit <= $shop->user->products()->where('published', 1)->count()
            ) {
                return 0;
            }
        }

        $product->save();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        if ($product->added_by == 'seller' && $request->approved == 1) {
            $shop = $product->user->shop;
            $followers = FollowSeller::where('shop_id', $shop->id)->get();

            if (!is_null($followers)) {
                $category = Category::findOrFail($product->category_id);
                $body = "🎉 New Product Alert from " . $product->user->name . "! 🎉<br>
                    Exciting news! " . $product->user->name . ", a seller you follow on " . env('APP_NAME') . ", has just added a brand new product<br>
                    Product Preview: <br>
                    1) Product Name: " . $product->name . "<br>
                    2) Category: " . $category->name . " <br>
                    3)Price: " . $product->unit_price . "<br>
                    4)Short Description: " . $product->unit_price . "<br>
                    Want to see more? Dive in to explore details, reviews, and purchase options!<br>
                    👉 <a href='" . url('/product/' . $product->slug) . "' class='btn btn-primary btn-sm'>View Product</a>";

                foreach ($followers as $follower) {
                    sendNotification($follower->user_id, "new_product_arrived", $product->slug, $product->id, null, $body);
                }
            }

            $body = "Product Approval<br>
                    Good news! Your product " . $product->name . " has been approved by the admin. You can now start selling on our platform.";

            sendSellerNotification($product->user_id, 'product_published', $product->slug, $product->id, null, $body);

            // Send to Mail for Seller
            $seller = User::where('id', $product->user_id)->first();
            $array['view'] = 'emails.quotationReplyMail';
            $array['subject'] = translate("Your Product Submission Has Been Approved!");
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['link'] = url('/');
            $array['content'] = "
            Dear " . $seller->name . ",<br>
            <br>
             We are pleased to inform you that your product, " . $product->name . ", has been reviewed and approved by our administration team.<br>
            Product Details:<br>
            1) Name: " . $product->name . ",<br>
            2) Description: " . $product->description . ", <br>
            3) Submission Date: " . $product->created_at . " Starting " . $product->updated_at . ",<br>
            Your product will be live and available for purchase on our platform.
            Should you have any questions or require further information, please don't hesitate to reach out to our support team at ".get_setting('contact_email')." or call us at  ".get_setting('contact_phone').".<br>
            <br>
            Thank you for choosing <a href=" . url('/') . "> Timber buddy </a> as your selling platform. We look forward to seeing your product thrive in our marketplace!<br>
            <br>
            Warm regards,<br>
            " . env('APP_NAME') . "";
            $array['title'] = "Your Product Submission Has Been Approved!";
            Mail::to($seller->email)->send(new EmailManager($array));
        }

        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->featured = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

    public function sku_combination(Request $request)
    {
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                if (isset($request[$name])) {
                    $data = array();
                    foreach ($request[$name] as $key => $item) {
                        // array_push($data, $item->value);
                        array_push($data, $item);
                    }
                    array_push($options, $data);
                }
            }
        }

        $combinations = (new CombinationService())->generate_combination($options);
        return view('backend.product.products.sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'));
    }

    public function sku_combination_edit(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $product_name = $request->name;
        $unit_price = $request->unit_price;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                if (isset($request[$name])) {
                    $data = array();
                    foreach ($request[$name] as $key => $item) {
                        // array_push($data, $item->value);
                        array_push($data, $item);
                    }
                    array_push($options, $data);
                }
            }
        }

        $combinations = (new CombinationService())->generate_combination($options);
        return view('backend.product.products.sku_combinations_edit', compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product'));
    }
}
