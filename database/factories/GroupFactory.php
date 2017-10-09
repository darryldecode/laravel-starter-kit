<?php

use Faker\Generator as Faker;

$factory->define(App\Group::class, function (Faker $faker) {
    $permissionKeys = [
        ['permission'=>'user.create','title'=>'Create User','description'=>'some description'],
        ['permission'=>'user.edit','title'=>'Edit User','description'=>'some description'],
        ['permission'=>'user.delete','title'=>'Delete User','description'=>'some description'],
    ];
    $permissionValues = [1,-1];

    $permissions = [];
    for ($i=0;$i<5;$i++)
    {
        $p = $permissionKeys[rand(0,2)];
        $p['value'] = $permissionValues[rand(0,1)];

        $permissions[] = $p;
    }

    return [
        'name' => ucwords($faker->words(2,true)),
        'permissions' => $permissions
    ];
});
