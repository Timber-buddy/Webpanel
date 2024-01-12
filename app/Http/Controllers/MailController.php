<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use  App\Mail\DemoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Product;

class MailController extends Controller
{
    public function index($userId)
    {
        $user = DB::table('users')->where('id', $userId)->first();
        $email = "wendyknapp.moon@gmail.com";
        $mailData = [
            'title' => 'TimberBuddy',
            'body' => 'Timber'
        ];

        try {
            dd(Mail::to($email)->send(new DemoMail($mailData)));
            echo "Success";
        } catch (Exception $e) {
            echo "Error";
        }
        // return ;

    }

    public function cron_test()
    {
        DB::table('cron_log')->insert(['data' => "Cron started"]);

        try {
            $sellers = User::where('user_type', 'seller')->where('banned', 0)->pluck('id')->toArray();

            foreach ($sellers as $key => $seller_id)
            {
                $subscriptionStatus = Subscription::where('user_id', $seller_id)->whereIn('status', ['C', 'S'])->orderBy('id', 'desc')->first();

                if (!is_null($subscriptionStatus))
                {
                    $curentTime = strtotime(date('Y-m-d'));
                    $expiryTime = strtotime($subscriptionStatus->valid_upto);
                    $difference = $expiryTime - $curentTime;

                    if($difference <= 0)
                    {
                        Product::where('user_id', $seller_id)->update(['published' => 0]);
                    }
                }
                else
                {
                    Product::where('user_id', $seller_id)->update(['published' => 0]);
                }

                DB::table('cron_log')->insert(['data' => $seller_id]);
            }
        } catch (Exception $e) {
            DB::table('cron_log')->insert(['data' => json_encode($e)]);
        }

        echo "Success";
    }
}
