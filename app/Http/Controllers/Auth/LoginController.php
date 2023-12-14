<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GeneaLabs\LaravelSocialiter\Facades\Socialiter;
use Socialite;
use App\Models\User;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\Subscription;
use App\Services\SocialRevoke;
use Session;
use Illuminate\Http\Request;
use CoreComponentRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Auth;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    /*protected $redirectTo = '/';*/


    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */

    //  mycode starts
    public function newlogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please Check the Form.');
        }

        $user = User::whereIn('user_type', ['customer'])
            ->where('email', $request->email)
            ->first();
        // return $user;

        if (!$user) {
            // return back()->with('error', 'Invalid Email');
            return "Invalid Email";
        }
        if (!$user || !Hash::check($request->password, $user->password)) {
            // return back()->with('error', 'Invalid Password');
            return "Invalid Password";
        }
        // return "login";
        return redirect()->route('home');
    }
    //  mycode starts

    public function redirectToProvider($provider)
    {
        if (request()->get('query') == 'mobile_app') {
            request()->session()->put('login_from', 'mobile_app');
        }
        if ($provider == 'apple') {
            return Socialite::driver("sign-in-with-apple")
                ->scopes(["name", "email"])
                ->redirect();
        }
        return Socialite::driver($provider)->redirect();
    }

    public function handleAppleCallback(Request $request)
    {
        try {
            $user = Socialite::driver("sign-in-with-apple")->user();
        } catch (\Exception $e) {
            flash(translate("Something Went wrong. Please try again."))->error();
            return redirect()->route('user.login');
        }
        //check if provider_id exist
        $existingUserByProviderId = User::where('provider_id', $user->id)->first();

        if ($existingUserByProviderId) {
            $existingUserByProviderId->access_token = $user->token;
            $existingUserByProviderId->refresh_token = $user->refreshToken;
            if (!isset($user->user['is_private_email'])) {
                $existingUserByProviderId->email = $user->email;
            }
            $existingUserByProviderId->save();
            //proceed to login
            auth()->login($existingUserByProviderId, true);
        } else {
            //check if email exist
            $existing_or_new_user = User::firstOrNew([
                'email' => $user->email
            ]);
            $existing_or_new_user->provider_id = $user->id;
            $existing_or_new_user->access_token = $user->token;
            $existing_or_new_user->refresh_token = $user->refreshToken;
            $existing_or_new_user->provider = 'apple';
            if (!$existing_or_new_user->exists) {
                $existing_or_new_user->name = 'Apple User';
                if ($user->name) {
                    $existing_or_new_user->name = $user->name;
                }
                $existing_or_new_user->email = $user->email;
                $existing_or_new_user->email_verified_at = date('Y-m-d H:m:s');
            }
            $existing_or_new_user->save();

            auth()->login($existing_or_new_user, true);
        }

        if (session('temp_user_id') != null) {
            Cart::where('temp_user_id', session('temp_user_id'))
                ->update([
                    'user_id' => auth()->user()->id,
                    'temp_user_id' => null
                ]);

            Session::forget('temp_user_id');
        }

        if (session('link') != null) {
            return redirect(session('link'));
        } else {
            if (auth()->user()->user_type == 'seller') {
                return redirect()->route('seller.dashboard');
            }
            return redirect()->route('dashboard');
        }
    }
    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        if (session('login_from') == 'mobile_app') {
            return $this->mobileHandleProviderCallback($request, $provider);
        }
        try {
            if ($provider == 'twitter') {
                $user = Socialite::driver('twitter')->user();
            } else {
                $user = Socialite::driver($provider)->stateless()->user();
            }
        } catch (\Exception $e) {
            flash(translate("Something Went wrong. Please try again."))->error();
            return redirect()->route('user.login');
        }

        //check if provider_id exist
        $existingUserByProviderId = User::where('provider_id', $user->id)->first();

        if ($existingUserByProviderId) {
            $existingUserByProviderId->access_token = $user->token;
            $existingUserByProviderId->save();
            //proceed to login
            auth()->login($existingUserByProviderId, true);
        } else {
            //check if email exist
            $existingUser = User::where('email', '!=', null)->where('email', $user->email)->first();

            if ($existingUser) {
                //update provider_id
                $existing_User = $existingUser;
                $existing_User->provider_id = $user->id;
                $existing_User->provider = $provider;
                $existing_User->access_token = $user->token;
                $existing_User->save();

                //proceed to login
                auth()->login($existing_User, true);
            } else {
                //create a new user
                $newUser = new User;
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->email_verified_at = date('Y-m-d Hms');
                $newUser->provider_id = $user->id;
                $newUser->provider = $provider;
                $newUser->access_token = $user->token;
                $newUser->save();
                //proceed to login
                auth()->login($newUser, true);
            }
        }

        if (session('temp_user_id') != null) {
            Cart::where('temp_user_id', session('temp_user_id'))
                ->update([
                    'user_id' => auth()->user()->id,
                    'temp_user_id' => null
                ]);

            Session::forget('temp_user_id');
        }

        if (session('link') != null) {
            return redirect(session('link'));
        } else {
            if (auth()->user()->user_type == 'seller') {
                return redirect()->route('seller.dashboard');
            }
            return redirect()->route('dashboard');
        }
    }

    public function mobileHandleProviderCallback($request, $provider)
    {
        $return_provider = '';
        $result = false;
        if ($provider) {
            $return_provider = $provider;
            $result = true;
        }
        return response()->json([
            'result' => $result,
            'provider' => $return_provider
        ]);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin_old(Request $request)
    {
        $request->validate([
            'email'    => 'required_without:phone',
            'phone'    => 'required_without:email',
            'password' => 'required|string',
        ]);

    }


    public function checkEmailExists(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if ($user !== null) {
            if (Hash::check($password, $user->password)) {
                return response()->json(['exists' => true, 'emailexists' => true]);
            } else {
                return response()->json(['exists' => true, 'emailexists' => false]);
            }
        } else {
            return response()->json(['exists' => false, 'emailexists' => false]);
        }
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required_without:phone',
            'phone'    => 'required_without:email',
            'password' => 'required|string',
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        if ($request->get('phone') != null) {
            return ['phone' => "+{$request['country_code']}{$request['phone']}", 'password' => $request->get('password')];
        } elseif ($request->get('email') != null) {
            return $request->only($this->username(), 'password');
        }
    }

    /**
     * Check user's role and redirect user based on their role
     * @return
     */
    public function authenticated()
    {
        if (session('temp_user_id') != null) {
            Cart::where('temp_user_id', session('temp_user_id'))
                ->update(
                    [
                        'user_id' => auth()->user()->id,
                        'temp_user_id' => null
                    ]
                );

            Session::forget('temp_user_id');
        }

        $user = User::find(auth()->user()->id);
        $user->login_status = $user->login_status + 1;
        $user->save();

        if (auth()->user()->login_status > 0) {
            if (auth()->user()->user_type == 'admin') {
                sendAdminNotification(auth()->user()->id, 'login_message');
            } elseif (auth()->user()->user_type == 'staff') {
                $roles = Auth()->user()->roles;
                if (is_null($roles[0]->created_by) || empty($roles[0]->created_by)) {
                    sendAdminNotification(auth()->user()->id, 'login_message');
                } else {
                    sendSellerNotification(auth()->user()->id, 'login_message');
                }
            } elseif (auth()->user()->user_type == 'seller') {
                sendSellerNotification(auth()->user()->id, 'login_message');
            } else {
                sendNotification(auth()->user()->id, 'login_message');
            }
        }

        if (auth()->user()->user_type == 'admin') {
            // if (Hash::check(request('password'), $user->password)) {
            //     // Password is correct
            //     if (request('remember')) {
            //         auth()->login($user, true);
            //         return redirect()->route('admin.dashboard');
            //     } else {
            //         auth()->login($user, false);
            //     }
            // }
            //CoreComponentRepository::instantiateShopRepository();
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->user_type == 'seller') {
            $subscription = Subscription::with('plan')->where('status', 'S')->orderBy('id', 'desc')->first();
            if (!is_null($subscription)) {
                $date1 = strtotime($subscription->valid_upto);
                $date2 = strtotime(date('Y-m-d'));
                $diff = $date2 - $date1;
                $days = floor($diff / (60 * 60 * 24));

                if ($days < 10) {
                    $body = "🔔 <b>Subscription Expiry Alert!</b> 🔔<br>
                        Your current subscription plan on " . env('APP_NAME') . " is about to expire!<br>
                        Plan Name: " . $subscription->plan->title . ", <br>
                        Expiry Date: " . date('d F, Y', strtotime($subscription->valid_upto)) . " <br>
                        <b>Take Action:</b> <br>
                        🔸 Renew now to continue enjoying uninterrupted benefits. <br>
                        🔸 Explore our other plans that might better fit your needs. <br>
                        Ensure your visibility and premium access by renewing on time. <br>
                        We're here to help if you have questions or need assistance!";

                    sendSellerNotification(Auth::user()->id, "seler_subscription_expiry", null, null, null, $body);
                }
            }

            return redirect()->route('seller.dashboard');
        } elseif (auth()->user()->user_type == 'staff') {
            $roles = Auth()->user()->roles;
            if (is_null($roles[0]->created_by) || empty($roles[0]->created_by)) {
                // CoreComponentRepository::instantiateShopRepository();
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('seller.dashboard');
            }
        } else {
            if (session('link') != null) {
                return redirect(session('link'));
            } else {
                return redirect()->route('dashboard');
            }
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        flash(translate('Invalid login credentials'))->error();
        return back();
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if (auth()->user() != null && (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')) {
            $redirect_route = 'login';
        } else {
            $redirect_route = 'home';
        }

        //User's Cart Delete
        // if (auth()->user()) {
        //     Cart::where('user_id', auth()->user()->id)->delete();
        // }

        $user = User::find(auth()->user()->id);
        $user->login_status = $user->login_status - 1;
        $user->save();

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->route($redirect_route);
    }

    public function account_deletion(Request $request)
    {

        $redirect_route = 'home';

        if (auth()->user()) {
            Cart::where('user_id', auth()->user()->id)->delete();
        }

        // if (auth()->user()->provider) {
        //     $social_revoke =  new SocialRevoke;
        //     $revoke_output = $social_revoke->apply(auth()->user()->provider);

        //     if ($revoke_output) {
        //     }
        // }

        $auth_user = auth()->user();

        // user images delete from database and file storage
        $uploads = $auth_user->uploads;
        if ($uploads) {
            foreach ($uploads as $upload) {
                if (env('FILESYSTEM_DRIVER') == 's3') {
                    Storage::disk('s3')->delete($upload->file_name);
                    if (file_exists(public_path() . '/' . $upload->file_name)) {
                        unlink(public_path() . '/' . $upload->file_name);
                        $upload->delete();
                    }
                } else {
                    unlink(public_path() . '/' . $upload->file_name);
                    $upload->delete();
                }
            }
        }

        $auth_user->customer_products()->delete();

        User::destroy(auth()->user()->id);

        auth()->guard()->logout();
        $request->session()->invalidate();

        $notificationData = [
            'name' => $user->name,
            'body' => Config('notification.customer_delete_account'),
            'thanks' => 'Thank you'
        ];

        try {
            \Notification::send($user, new CustomerRegistration($notificationData));
        } catch (Exception $e) {
            // dd($e);
            echo "<script>console.log('" . $e . "')</script>";
            // return back();
        }

        flash(translate("Your account deletion successfully done."))->success();
        return redirect()->route($redirect_route);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'account_deletion']);
    }



    //Test to Admin Login 29/11/2023.

    // public function showLoginForm()
    // {
    //     return view('admin.login');
    // }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');
    //     $user = User::where('email', $request->email)->first();
    //     if (!$user) {
    //         throw new \Exception('Invalid Email.');
    //     }
    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         throw new \Exception('Invalid password.');
    //         // return redirect()->route('superadmin.login');
    //     }
    //     else{
    //         // Authentication passed
    //         Auth::login($user);
    //         return redirect()->route('admin.dashboard');
    //     }

    //     // Authentication failed
    //     return redirect()->back()->withInput($request->only('email'));
    // }

}
