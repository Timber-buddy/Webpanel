<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use App\Models\Country;
use App\Models\State;

use Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // LOCATIONS //
        $jsonData = curl_get_file_contents("http://www.geoplugin.net/json.gp?ip=".getIp());
        $location = json_decode($jsonData);

        if (is_null($location->geoplugin_countryCode))
        {
            $countryCode = "IN";
        }
        else
        {
            $countryCode = $location->geoplugin_countryCode;
        }

        $defaultCountry = Country::where('code', $countryCode)->first();

        $data['locations'] = State::with('cities')->where('country_id', $defaultCountry->id)->get();

        return view('backend.admin_profile.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
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
        if(env('DEMO_MODE') == 'On'){
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $flag = 0;
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->city = $request->city;
        if($request->new_password != null && ($request->new_password == $request->confirm_password)){
            $user->password = Hash::make($request->new_password);
            $flag = 1;
        }
        $user->avatar_original = $request->avatar;
        if($user->save())
        {
            $adr = Address::where('user_id', $id)->first();
            if(is_null($adr))
            {
                $address = new Address;
                $address->user_id = $id;
                $address->city_id = $request->city;
                $address->save();
            }
            else
            {
                $adr->city_id = $request->city;
                $adr->save();
            }

            flash(translate('Your Profile has been updated successfully!'))->success();
            if($flag == 1)
            {
                $body = "🔒 Password Update Successful! 🔒
                        Hey ".auth()->user()->name.", We'd like to inform you that the password for your administrative account on ".env('APP_NAME')." has been successfully changed.<br>
                        If you didn't request this change then contact our technical team directly within the app.";
                sendAdminNotification(auth()->user()->id, 'admin_reset_password', null, null, null, $body);
            }
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
