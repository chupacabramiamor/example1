<?php

use App\Models\Plan;
use App\Services\SubscriptionService;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        Plan::truncate();

        $subscriptionSvc = app(SubscriptionService::class);

        $subscriptionSvc->syncPlans();
    }
}
