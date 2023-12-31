<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use Auth;
use App\Models\ProductsImport;
use App\Models\User;
use PDF;
use Excel;

class ProductBulkUploadController extends Controller
{
    public function index()
    {
        if (Auth::user()->user_type == "staff") 
        {
            $roles = Auth()->user()->roles;
            $id = $roles[0]->created_by;

            $user = User::with(['shop'])->find($id);

            Auth::user()->shop = $user->shop;
            Auth::user()->owner_id = $id;
        }

        if(Auth::user()->shop->verification_status){
            return view('seller.product.product_bulk_upload.index');
        }
        else{
            return redirect()->route('seller.dashboard')->with('error','Data Deleted');
        }
    }

    public function pdf_download_category()
    {
        $categories = Category::all();

        return PDF::loadView('backend.downloads.category',[
            'categories' => $categories,
        ], [], [])->download('category.pdf');
    }

    public function pdf_download_brand()
    {
        $brands = Brand::all();

        return PDF::loadView('backend.downloads.brand',[
            'brands' => $brands,
        ], [], [])->download('brands.pdf');
    }

    public function bulk_upload(Request $request)
    {
        if($request->hasFile('bulk_file')){
            $import = new ProductsImport;
            Excel::import($import, request()->file('bulk_file'));
        }
        
        return back();
    }

}
