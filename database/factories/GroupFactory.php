<?php

use Faker\Generator as Faker;

$factory->define(\App\Components\User\Models\Group::class, function (Faker $faker) {
    return [
        'name' => ucwords($faker->words(2,true)),
        'permissions' => []
    ];
});
