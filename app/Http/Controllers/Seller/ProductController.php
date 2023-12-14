<?php

namespace App\Http\Controllers\Seller;

use AizPackages\CombinationGenerate\Services\CombinationService;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTax;
use App\Models\ProductTranslation;
use App\Models\FollowSeller;
use App\Models\User;
use App\Models\Subscription;
use Carbon\Carbon;
use Combinations;
use Artisan;
use Auth;
use Str;
use DB;
use App\Services\ProductService;
use App\Services\ProductTaxService;
use App\Services\ProductFlashDealService;
use App\Services\ProductStockService;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $productService;
    protected $productTaxService;
    protected $productFlashDealService;
    protected $productStockService;
    protected $sellerId;

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
        $this->sellerId = getSellerId();
    }

    public function index(Request $request)
    {
        $search = null;
        $products = Product::where('user_id', $this->sellerId)->where('digital', 0)->where('auction_product', 0)->where('wholesale_product', 0)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $products = $products->where('name', 'like', '%' . $search . '%');
        }
        $products = $products->paginate(10);

        $product_count = Product::where('user_id', $this->sellerId)->where('digital', 0)->where('auction_product', 0)->where('wholesale_product', 0)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $product_count = $product_count->where('name', 'like', '%' . $search . '%');
        }
        $product_count = $product_count->where('published', 1)->count();

        $subscription = Subscription::with('plan')->where('user_id', $this->sellerId)->where('status', 'S')->whereDate('valid_upto', '>=', date('Y-m-d'))->orderBy('id', 'desc')->first();

        return view('seller.product.products.index', compact('products', 'search', 'subscription', 'product_count'));
    }

    public function create(Request $request)
    {
        if (addon_is_activated('seller_subscription'))
        {
            if (seller_package_validity_check(getSellerId()))
            {
                $categories = Category::where('parent_id', 0)
                    ->where('digital', 0)
                    ->with('childrenCategories')
                    ->get();
                return view('seller.product.products.create', compact('categories'));
            } else {
                flash(translate('Please upgrade your package.'))->warning();
                return back();
            }
        }
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('seller.product.products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        if (addon_is_activated('seller_subscription')) {
            if (!seller_package_validity_check(getSellerId())) {
                flash(translate('Please upgrade your package.'))->warning();
                return redirect()->route('seller.products');
            }
        }

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

        //Product Stock
        $this->productStockService->store($request->only([
            'colors_active', 'colors', 'choice_no', 'unit_price', 'sku', 'current_stock', 'product_id'
        ]), $product);

        // Product Translations
        $request->merge(['lang' => env('DEFAULT_LANGUAGE')]);
        ProductTranslation::create($request->only([
            'lang', 'name', 'unit', 'description', 'product_id'
        ]));

        flash(translate('Product has been inserted successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        $admin = User::where('user_type', 'admin')->first();
        $body = "🆕 New Product Submission!<br>
            Seller ".Auth::user()->name." has added a new product: ".$product->name.". Tap to review and approve.";
        sendAdminNotification($admin->id, 'new_product', $product->slug, $product->id, null, $body);

        return redirect()->route('seller.products');
    }

    public function edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($this->sellerId != $product->user_id) {
            flash(translate('This product is not yours.'))->warning();
            return back();
        }

        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('seller.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        //Product
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

        //VAT & Tax
        if ($request->tax_id) {
            ProductTax::where('product_id', $product->id)->delete();
            $request->merge(['product_id' => $product->id]);
            $this->productTaxService->store($request->only([
                'tax_id', 'tax', 'tax_type', 'product_id'
            ]));
        }

        // Product Translations
        // ProductTranslation::updateOrCreate(
        //     $request->only([
        //         'lang', 'product_id'
        //     ]),
        //     $request->only([
        //         'name', 'unit', 'description'
        //     ])
        // );


        flash(translate('Product has been updated successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

         return redirect()->route('seller.products');
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
                $data = array();
                foreach ($request[$name] as $key => $item) {
                    array_push($data, $item);
                }
                array_push($options, $data);
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
                $data = array();
                foreach ($request[$name] as $key => $item) {
                    array_push($data, $item);
                }
                array_push($options, $data);
            }
        }

        $combinations = (new CombinationService())->generate_combination($options);
        return view('backend.product.products.sku_combinations_edit', compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product'));
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

    public function updatePublished(Request $request)
    {
        if ($request->status == 1)
        {
            $subscription = Subscription::with('plan')->where('user_id', getSellerId())->whereIn('status', ['C', 'S'])->orderBy('id', 'desc')->first();
            $products = Product::where('user_id', getSellerId())->where('published', 1)->where('digital', 0)->where('auction_product', 0)->where('wholesale_product', 0)->count();

            if (is_null($subscription))
            {
                return 2;
            }
            else if ($products >= $subscription->plan->product_limit)
            {
                return 2;
            }
        }

        $product = Product::findOrFail($request->id);
        $product->published = $request->status;
        $product->save();

        $shop = $product->user->shop;
        $followers = FollowSeller::where('shop_id', $shop->id)->get();

        if(!is_null($followers))
        {
            $category = Category::findOrFail($product->category_id);
            $body = "🎉 New Product Alert from ".$product->user->name."! 🎉<br>
                Exciting news! ".$product->user->name.", a seller you follow on ".env('APP_NAME').", has just added a brand new product<br>
                Product Preview: <br>
                1) Product Name: ".$product->name."<br>
                2) Category: ".$category->name." <br>
                3)Price: ".$product->unit_price."<br>
                4)Short Description: ".$product->unit_price."<br>
                Want to see more? Dive in to explore details, reviews, and purchase options!<br>
                👉 <a href='".url('/product/'.$product->slug)."' class='btn btn-primary btn-sm'>View Product</a>";

            foreach ($followers as $follower)
            {
                sendNotification($follower->user_id, "new_product_arrived", $product->slug, $product->id);
            }
        }

        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->seller_featured = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

    public function duplicate($id)
    {
        $product = Product::find($id);
        if ($this->sellerId != $product->user_id) {
            flash(translate('This product is not yours.'))->warning();
            return back();
        }

        if (addon_is_activated('seller_subscription')) {
            if (!seller_package_validity_check(getSellerId())) {
                flash(translate('Please upgrade your package.'))->warning();
                return back();
            }
        }

        if ($this->sellerId == $product->user_id) {

            $subscription = Subscription::with('plan')->where('user_id', getSellerId())->whereIn('status', ['C', 'S'])->orderBy('id', 'desc')->first();
            $products = Product::where('user_id', getSellerId())->where('published', 1)->where('digital', 0)->where('auction_product', 0)->where('wholesale_product', 0)->count();

            if (is_null($subscription))
            {
                flash(translate('Please upgrade your package first'))->warning();
                return back();
            }
            else if ($products >= $subscription->plan->product_limit)
            {
                flash(translate('Please upgrade your package first'))->warning();
                return back();
            }

            $product_new = $product->replicate();
            $product_new->slug = $product_new->slug . '-' . Str::random(5);
            $product_new->save();

            //Product Stock
            $this->productStockService->product_duplicate_store($product->stocks, $product_new);

            //VAT & Tax
            $this->productTaxService->product_duplicate_store($product->taxes, $product_new);

            flash(translate('Product has been duplicated successfully'))->success();
            return redirect()->route('seller.products');
        } else {
            flash(translate('This product is not yours.'))->warning();
            return back();
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($this->sellerId != $product->user_id) {
            flash(translate('This product is not yours.'))->warning();
            return back();
        }

        $product->product_translations()->delete();
        $product->stocks()->delete();
        $product->taxes()->delete();
        $quotationIds = DB::table('quotation')->where('product_id', $id)->pluck('id')->toArray();
        DB::table('quotation_message')->whereIn('quotation_id', $quotationIds)->delete();
        DB::table('quotation_attribute_data')->whereIn('quotaton_id', $quotationIds)->delete();
        DB::table('quotation_attribute')->whereIn('quotation_id', $quotationIds)->delete();

        if (Product::destroy($id)) {
            Cart::where('product_id', $id)->delete();

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
}
