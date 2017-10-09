<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    $permissionKeys = [
        ['permission'=>'user.create','title'=>'Create User','description'=>'some description'],
        ['permission'=>'user.edit','title'=>'Edit User','description'=>'some description'],
        ['permission'=>'user.delete','title'=>'Delete User','description'=>'some description'],
    ];
    $permissionValues = [1,0,-1];

    $permissions = [];
    for ($i=0;$i<5;$i++)
    {
        $p = $permissionKeys[rand(0,2)];
        $p['value'] = $permissionValues[rand(0,2)];

        $permissions[] = $p;
    }

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'permissions' => $permissions,
        'last_login' => $faker->dateTime,
        'active' => null,
        'activation_key' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
    ];
});
