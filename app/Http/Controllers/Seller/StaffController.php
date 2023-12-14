<?php

namespace App\Http\Controllers\Seller;

use App\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerPermission;
use Spatie\Permission\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Auth;
use Hash;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::where('shop_id', getSellerId())->paginate(10);
        return view('seller.xyz', compact('staffs'));
    }
    
    public function add()
    {
        $roles = Role::where('created_by', getSellerId())->orderBy('id', 'desc')->get();
        return view('seller.add', compact('roles'));
    }

    public function permission()
    {
        $roles = Role::where('id','!=',1)->where('created_by', getSellerId())->paginate(10);
        return view('seller.permission', compact('roles'));
    }

    public function store(Request $request)
    {
        if(User::where('email', $request->email)->first() == null){
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->mobile;
            $user->user_type = "staff";
            $user->password = Hash::make($request->password);
            if($user->save())
            {
                $staff = new Staff;
                $staff->user_id = $user->id;
                $staff->role_id = $request->role_id;
                $staff->shop_id = getSellerId();
                $user->assignRole(Role::findOrFail($request->role_id)->name);
                if($staff->save()){
                    flash(translate('Staff has been inserted successfully'))->success();

                    sendSellerNotification($user->id, 'new_staff_seller');

                    return redirect()->route('seller.staff.all');
                }
            }
        }

        flash(translate('Email already used'))->error();
        return back();
    }

    public function destroy($id)
    {
        User::destroy(Staff::findOrFail($id)->user->id);
        if(Staff::destroy($id)){
            flash(translate('Staff has been deleted successfully'))->success();
            return redirect()->route('seller.staff.all');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail(decrypt($id));
        $roles = Role::where('created_by', getSellerId())->orderBy('id', 'desc')->get();
        return view('seller.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $user = $staff->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->mobile;
        if(strlen($request->password) > 0){
            $user->password = Hash::make($request->password);
        }
        if($user->save()){
            $staff->role_id = $request->role_id;
            if($staff->save()){
                $user->syncRoles(Role::findOrFail($request->role_id)->name);
                flash(translate('Staff has been updated successfully'))->success();
                return redirect()->route('seller.staff.all');
            }
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }
}