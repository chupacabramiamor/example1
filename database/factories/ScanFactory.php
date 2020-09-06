<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models;
use Faker\Generator as Faker;

$factory->define(Models\Scan::class, function (Faker $faker) {
    $website = factory(Models\Website::class)->create();

    return [
        'website_id' => $website->id
    ];
});
