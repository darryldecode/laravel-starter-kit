<?php

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
        $this->seedPermissions();
        $this->seedGroups();
        $this->seedUsers();
    }

    protected function seedUsers()
    {
        // create admin account
        $AdminUser = \App\User::create([
            'name' => 'DarrylDecode',
            'email' => 'darryl@vizsion.com',
            'password' => '12345678',
            'remember_token' => str_random(10),
            'permissions' => [],
            'last_login' => \Carbon\Carbon::now(),
            'active' => \Carbon\Carbon::now(),
            'activation_key' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        ]);

        // make super user
        $AdminUser->groups()->attach(\App\Group::SUPER_USER_GROUP_ID);

        // generate random users
        $users = factory(\App\User::class,30)->create();
        $users->each(function($u)
        {
            $u->groups()->attach(\App\Group::DEFAULT_USER_GROUP_ID);
        });
    }

    protected function seedPermissions()
    {
        // create the super user permission
        \App\Permission::create([
            'title' => 'Super User',
            'description' => 'Superuser permission',
            'permission' => \App\Permission::SUPER_USER_PERMISSION,
        ]);

        // create all other permissions
        \App\Permission::create([
            'title' => 'User Create',
            'description' => 'Permission to create user.',
            'permission' => 'user.create',
        ]);
        \App\Permission::create([
            'title' => 'User Edit',
            'description' => 'Permission to edit user.',
            'permission' => 'user.edit',
        ]);
        \App\Permission::create([
            'title' => 'User Delete',
            'description' => 'Permission to delete user.',
            'permission' => 'user.delete',
        ]);
    }

    protected function seedGroups()
    {
        // create super user group
        \App\Group::create([
            'name' => 'Super User',
            'permissions' => [
                [
                    'title' => 'Super User',
                    'description' => 'Superuser permission',
                    'permission' => \App\Permission::SUPER_USER_PERMISSION,
                    'value' => 1
                ]
            ]
        ]);

        // create normal user
        \App\Group::create([
            'name' => 'User',
            'permissions' => []
        ]);
        \App\Group::create([
            'name' => 'Moderator',
            'permissions' => []
        ]);
    }
}
