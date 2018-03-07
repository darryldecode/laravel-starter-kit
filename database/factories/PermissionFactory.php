<?php

use Faker\Generator as Faker;

$factory->define(\App\Components\User\Models\Permission::class, function (Faker $faker) {
    return [
        'title' => ucwords($faker->words(3,true)),
        'description' => $faker->text(300),
        'key' => str_replace(' ','',$faker->words(3,true)),
    ];
});
