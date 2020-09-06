<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Website;
use Faker\Generator as Faker;

$factory->define(Website::class, function (Faker $faker) {
    $user = User::where('type', User::TYPE_CUSTOMER)->inRandomOrder()->first();

    $subscription = Subscription::create([
        'user_id' => $user->id,
        'plan_id' => Plan::inRandomOrder()->first()->id,
        // 'website_id' => $website_id,
        'status' => Subscription::STATUS_ACTIVE,
        'update_url' => $this->faker->url,
        'cancel_url' => $this->faker->url
    ]);

    return [
        'domain' => $faker->domainName,
        'protocol' => 'http',
        'state' => Website::STATE_COMPLETED,
        'subscription_id' => $subscription->id
    ];
});
