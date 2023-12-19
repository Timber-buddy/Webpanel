<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\RoleTranslation;
use Spatie\Permission\Models\Role;
use App\Models\SellerPermission;
use Auth;
use DB;

class RoleController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        // $this->middleware(['permission:view_staff_roles'])->only('index');
        // $this->middleware(['permission:add_staff_role'])->only('create');
        // $this->middleware(['permission:edit_staff_role'])->only('edit');
        // $this->middleware(['permission:delete_staff_role'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::where('id','!=',1)->where('created_by', Auth::user()->id)->paginate(10);
        return view('seller.permission', compact('roles'));

        // $roles = Role::paginate(10);
        // return view('backend.staff.staff_roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission_groups = SellerPermission::all()->groupBy('section');
        return view('seller.add_permission', compact('permission_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $role = Role::create(['name' => $request->name, 'created_by' => Auth::user()->id]);
        $role = Role::create(['name' => $request->name, 'created_by' => getSellerId()]);
        $role->givePermissionTo($request->permissions);

        $role_translation = RoleTranslation::firstOrNew(['lang' => 'en', 'role_id' => $role->id]);
        $role_translation->name = $request->name;
        $role_translation->save();

        flash(translate('New Role has been added successfully'))->success();
        // return redirect()->route('seller.roles.index');
        return redirect()->route('seller.staff.permissions');
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
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $role = Role::findOrFail($id);
        $permission_groups = SellerPermission::all()->groupBy('section');
        $allowed_permissions = DB::table('role_has_permissions')->where('role_id', $id)->pluck('permission_id')->toArray();
        return view('seller.edit_permission', compact('role','lang','permission_groups','allowed_permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        if($request->lang == 'en'){
            $role->name = $request->name;
        }
        $role->syncPermissions($request->permissions);
        $role->save();

        // Role Translation
        $role_translation = RoleTranslation::firstOrNew(['lang' => 'en', 'role_id' => $role->id]);
        $role_translation->name = $request->name;
        $role_translation->save();

        flash(translate('Role has been updated successfully'))->success();
        return back();
        // return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        RoleTranslation::where('role_id',$id)->delete();
        Role::destroy($id);
        flash(translate('Role has been deleted successfully'))->success();
        return redirect()->route('seller.roles.index');
    }
}
