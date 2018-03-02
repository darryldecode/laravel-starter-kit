<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 3/3/2018
 * Time: 12:42 AM
 */

namespace Tests\Unit\User;


use App\Components\User\Models\Group;
use App\Components\User\Models\Permission;
use App\Components\User\Repositories\MySQLUserRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class UserGroupTest extends TestCase
{
    /**
     * @var MySQLUserRepository
     */
    protected $userRepo;

    protected $group;

    protected $permission;

    public function setUp()
    {
        parent::setUp();

        $this->userRepo = new MySQLUserRepository();
        $this->group = factory(Group::class)->create();
        $this->permission = factory(Permission::class)->create();
    }

    public function test_it_can_assign_user_to_a_group()
    {
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
            'groups' => [
                $this->group->id => true
            ]
        ])->getData();

        $this->assertTrue($user->inGroup($this->group));
        $this->assertTrue($user->inGroup($this->group->name));
        $this->assertTrue($user->inGroup($this->group->id));
    }

    public function test_user_should_not_be_in_group()
    {
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
            'groups' => [
                $this->group->id => false
            ]
        ])->getData();

        $this->assertFalse($user->inGroup($this->group));
        $this->assertFalse($user->inGroup($this->group->name));
        $this->assertFalse($user->inGroup($this->group->id));
    }
}