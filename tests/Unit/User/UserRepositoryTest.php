<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 3/3/2018
 * Time: 12:41 AM
 */

namespace Tests\Unit\User;


use App\Components\User\Models\Group;
use App\Components\User\Models\Permission;
use App\Components\User\Models\User;
use App\Components\User\Repositories\UserRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    /**
     * @var UserRepository
     */
    protected $userRepo;

    protected $group;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepo = new UserRepository(new User());
        $this->group = factory(Group::class)->create();
        $this->permission = factory(Permission::class)->create();
    }

    public function test_it_can_create_user()
    {
        $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
        ]);

        $this->assertDatabaseHas('users',[
            'name' => 'john'
        ]);
    }

    public function test_it_can_update_user()
    {
        /** @var User $user */
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
        ]);

        $this->userRepo->update($user->id,[
            'name' => 'new name',
            'email' => 'newemail@gmail.com',
        ]);

        $this->assertDatabaseHas('users',[
            'name' => 'new name',
            'email' => 'newemail@gmail.com',
        ]);
    }

    public function test_it_can_delete_user()
    {
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
        ]);

        $this->assertDatabaseHas('users',[
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $this->userRepo->delete($user->id);

        $this->assertDatabaseMissing('users',[
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}