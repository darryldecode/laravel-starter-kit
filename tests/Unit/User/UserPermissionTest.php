<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 3/3/2018
 * Time: 12:43 AM
 */

namespace Tests\Unit\User;


use App\Components\User\Models\Group;
use App\Components\User\Models\Permission;
use App\Components\User\Models\User;
use App\Components\User\Repositories\MySQLUserRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class UserPermissionTest extends TestCase
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

    public function test_it_can_check_if_user_has_special_permission()
    {
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [
                ['permission'=>$this->permission->permission, 'value'=>1]
            ],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
            'groups' => [
                $this->group->id => true
            ]
        ])->getData();

        $this->assertTrue($user->hasPermission($this->permission->permission));
        $this->assertTrue($user->hasAnyPermission([$this->permission->permission]));
    }

    public function test_it_can_add_user_special_permission()
    {
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
            'groups' => []
        ])->getData();
        
        // remove
        $user->addPermission($this->permission,User::PERMISSION_ALLOW);

        $this->assertTrue($user->hasPermission($this->permission->permission));
        $this->assertTrue($user->hasAnyPermission([$this->permission->permission]));
    }

    public function test_it_can_remove_user_special_permission()
    {
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [
                ['permission'=>$this->permission->permission, 'value'=>1]
            ],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
            'groups' => [
                $this->group->id => true
            ]
        ])->getData();

        $this->assertTrue($user->hasPermission($this->permission->permission));
        $this->assertTrue($user->hasAnyPermission([$this->permission->permission]));

        // remove
        $user->removePermission($this->permission);

        $this->assertFalse($user->hasPermission($this->permission->permission));
        $this->assertFalse($user->hasAnyPermission([$this->permission->permission]));
    }

    public function test_user_can_inherit_to_group_permission()
    {
        $this->group->addPermission($this->permission->id,Group::PERMISSION_ALLOW);

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

        $this->assertTrue($user->hasPermission($this->permission->permission));
        $this->assertTrue($user->hasAnyPermission([$this->permission->permission]));
    }

    public function test_user_special_permission_has_higher_priority_than_group_inherited_permission()
    {
        $this->group->addPermission($this->permission->id,Group::PERMISSION_ALLOW);

        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [
                ['permission'=>$this->permission->permission, 'value'=>-1]
            ],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
            'groups' => [
                $this->group->id => true
            ]
        ])->getData();

        $this->assertFalse($user->hasPermission($this->permission->permission));
        $this->assertFalse($user->hasAnyPermission([$this->permission->permission]));
    }

    public function test_can_add_permission_to_group()
    {
        $this->group->addPermission($this->permission->id,Group::PERMISSION_ALLOW);

        $this->assertTrue($this->group->hasPermission($this->permission->permission));
    }

    public function test_can_remove_permission_to_group()
    {
        $this->group->removePermission($this->permission->id);

        $this->assertFalse($this->group->hasPermission($this->permission->permission));
    }
}