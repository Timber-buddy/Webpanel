<?php

namespace App\Http\Controllers;

use Hash;
use App\Models\User;
use App\Models\State;
use App\Models\Address;
use App\Models\Country;

use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\SellerQuotationReplyMail;

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

                //Mail
              $array['view'] = 'emails.quotationReplyMail';
              $array['subject'] = translate("Security Alert: Password Change Confirmation for Admin Account!");
              $array['from'] = env('MAIL_FROM_ADDRESS');
              $array['content'] = "
              Dear ".auth()->user()->name.",<br>
              <br>
               We're reaching out to confirm that the password for your administrative account on ".env('APP_NAME')." was recently updated.
              If you initiated this change, please ensure that you store your new password securely and avoid using the same password across multiple platforms or services.<br>
              If you did not request or authorize this password change, it's essential to act immediately:
              Immediate Action: Reset your password using the 'Forgot Password' feature on our admin login page.
              Review Account Activity: Log in and verify any recent changes or activities in the admin dashboard to ensure no unauthorized actions took place.<br>
              Contact Support: If you notice any discrepancies or need assistance, please reach out to our technical support team at [support@email.com] or call [Support Phone Number].<br>
              <br>
              Your account's security is of the utmost importance, especially given its administrative privileges. We recommend regularly updating your password and considering two-factor authentication, if not already enabled.<br>
              <br>
              Best regards,<br>
              [Platform/Website Security Team]<br>
              ".env('APP_NAME')."";
              $array['title'] = "Security Alert: Password Change Confirmation for Admin Account!";
              Mail::to(auth()->user()->email)->send(new SellerQuotationReplyMail($array));
            }
            Session::put(['message' => 'Your Profile has been updated successfully', 'SmgStatus' => 'success']);
            return back();
        }

        Session::put(['message' => 'Sorry! Something went wrong.', 'SmgStatus' => 'danger']);
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
