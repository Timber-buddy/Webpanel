<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Quotation;
use Auth;
use Artisan;
use Cache;
use App\Models\User;
use CoreComponentRepository;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_dashboard(Request $request)
    {
        $sort_by = 0;
        if (Auth::user()->user_type == "admin")
        {
            $userMasterId = Auth::user()->id;
        }
        else
        {
            $admin = User::where('user_type', 'admin')->first();
            $userMasterId= $admin->id;
        }


        $quotations = Quotation::get();
        if ($request->verification_status == 1) {
            $sort_by = $request->verification_status;
            $quotations = Quotation::where('seller_id', $userMasterId)->get();
        }

        if ($request->verification_status == 2) {
            $sort_by = $request->verification_status;
            $quotations = Quotation::where('seller_id', '!=', $userMasterId)->get();
        }
        //dd($quotations);
        $categoryNames = $quotations->pluck('product.category.name');
        $quotations = $quotations->map(function ($quotation, $key) use ($categoryNames) {
            $quotation->categoryName = $categoryNames[$key];
            return $quotation;
        });

        CoreComponentRepository::initializeCache();
        $root_categories = Category::where('level', 0)->get();

        $cached_graph_data = Cache::remember('cached_graph_data', 60, function () use ($root_categories) {
            $num_of_sale_data = null;
            $qty_data = null;
            foreach ($root_categories as $key => $category) {
                $category_ids = \App\Utility\CategoryUtility::children_ids($category->id);
                $category_ids[] = $category->id;

                $products = Product::with('stocks')->whereIn('category_id', $category_ids)->get();
                $qty = 0;
                $sale = 0;
                foreach ($products as $key => $product) {
                    $sale += $product->num_of_sale;
                    foreach ($product->stocks as $key => $stock) {
                        $qty += $stock->qty;
                    }
                }
                $qty_data .= $qty . ',';
                $num_of_sale_data .= $sale . ',';
            }
            $item['num_of_sale_data'] = $num_of_sale_data;
            $item['qty_data'] = $qty_data;

            return $item;
        });

        return view('backend.dashboard', compact('root_categories', 'cached_graph_data', 'quotations', 'sort_by'));
    }

    function clearCache(Request $request)
    {
        Artisan::call('optimize:clear');
        flash(translate('Cache cleared successfully'))->success();
        return back();
    }
}
