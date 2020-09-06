<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Cookie;
use App\Models\Group;
use Faker\Generator as Faker;

$factory->define(Cookie::class, function (Faker $faker) {
    return [
        'group_id' => $faker->randomElement([ Group::IDENT_UNCLASSIFIED, Group::IDENT_NECESSARY, Group::IDENT_PREFERENCES, Group::IDENT_STATISTICS, Group::IDENT_ADVERTISING ]),
        'name' => $faker->word,
        'provider' => $faker->domainName
    ];
});
