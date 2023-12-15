<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Product;
use DB;

class SubscriptionCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
    }
}
