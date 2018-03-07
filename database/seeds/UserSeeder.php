<?php

use App\Components\User\Models\Group;
use App\Components\User\Models\Permission;
use App\Components\User\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create the super user permission
        $permissionSuperUser = Permission::create([
            'title' => 'Super User',
            'description' => 'Superuser permission',
            'permission' => Permission::SUPER_USER_PERMISSION,
        ]);

        // create all other permissions
        $permissionSample1 = Permission::create([
            'title' => 'User Create',
            'description' => 'Permission to create user. This is an example permission only',
            'permission' => 'user.create',
        ]);
        $permissionSample2 = Permission::create([
            'title' => 'User Edit',
            'description' => 'Permission to edit user. This is an example permission only',
            'permission' => 'user.edit',
        ]);
        $permissionSample3 = Permission::create([
            'title' => 'User Delete',
            'description' => 'Permission to delete user. This is an example permission only',
            'permission' => 'user.delete',
        ]);

        // create super user group
        $groupSuperUser = Group::create([
            'name' => Group::SUPER_USER_GROUP_NAME,
            'permissions' => [
                [
                    'title' => 'Super User',
                    'description' => 'Superuser permission',
                    'permission' => Permission::SUPER_USER_PERMISSION,
                    'value' => 1
                ]
            ]
        ]);
        $groupSuperUser->addPermission($permissionSuperUser,Group::PERMISSION_ALLOW);

        // create normal user
        $groupDefaultUser = Group::create([
            'name' => Group::DEFAULT_USER_GROUP_NAME,
            'permissions' => []
        ]);

        // create admin account
        $AdminUser = User::create([
            'name' => 'John Doe',
            'email' => 'admin@gmail.com',
            'password' => '12345678',
            'remember_token' => str_random(10),
            'permissions' => [],
            'last_login' => \Carbon\Carbon::now(),
            'active' => \Carbon\Carbon::now(),
            'activation_key' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        ]);

        // make super user
        $AdminUser->groups()->attach($groupSuperUser);

        // generate random users
        $users = factory(User::class,30)->create();
        $users->each(function($u) use ($groupDefaultUser)
        {
            $u->groups()->attach($groupDefaultUser);
        });
    }
}
