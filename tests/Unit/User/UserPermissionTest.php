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
use App\Components\User\Repositories\GroupRepository;
use App\Components\User\Repositories\PermissionRepository;
use App\Components\User\Repositories\UserRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    /**
     * @var UserRepository
     */
    protected $userRepo;

    /**
     * @var GroupRepository
     */
    protected $groupRepo;

    /**
     * @var PermissionRepository
     */
    protected $permissionRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepo = new UserRepository(new User());
        $this->groupRepo = new GroupRepository(new Group());
        $this->permissionRepo = new PermissionRepository(new Permission());
    }

    public function test_it_can_check_if_user_has_special_permission()
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepo->create([
            'title' => 'Permission 1',
            'description' => 'some description',
            'key' => 'permission.1',
        ]);

        /** @var User $user */
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString()
        ]);

        $user->addPermission($permission,Permission::PERMISSION_ALLOW);

        $this->assertTrue($user->hasPermission($permission->key));
        $this->assertTrue($user->hasAnyPermission([$permission->key]));
    }

    public function test_it_can_add_user_special_permission()
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepo->create([
            'title' => 'Permission 1',
            'description' => 'some description',
            'key' => 'permission.1',
        ]);

        /** @var User $user */
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
            'groups' => []
        ]);

        // add
        $user->addPermission($permission,Permission::PERMISSION_ALLOW);

        $this->assertTrue($user->hasPermission($permission->key));
        $this->assertTrue($user->hasAnyPermission([$permission->key]));
    }

    public function test_it_can_remove_user_special_permission()
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepo->create([
            'title' => 'Permission 1',
            'description' => 'some description',
            'key' => 'permission.1',
        ]);

        /** @var User $user */
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString()
        ]);

        // add
        $user->addPermission($permission,Permission::PERMISSION_ALLOW);

        $this->assertTrue($user->hasPermission($permission->key));
        $this->assertTrue($user->hasAnyPermission([$permission->key]));

        // remove
        $user->removePermission($permission);

        $this->assertFalse($user->hasPermission($permission->key));
        $this->assertFalse($user->hasAnyPermission([$permission->key]));
    }

    public function test_user_can_inherit_to_group_permission()
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepo->create([
            'title' => 'Permission 1',
            'description' => 'some description',
            'key' => 'permission.1',
        ]);

        /** @var Group $group */
        $group = $this->groupRepo->create([
            'name' => 'Group 1',
            'permissions' => [],
        ]);

        $group->addPermission($permission,Permission::PERMISSION_ALLOW);

        /** @var User $user */
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
        ]);

        $user->groups()->attach($group);

        $this->assertTrue($user->hasPermission($permission->key));
        $this->assertTrue($user->hasAnyPermission([$permission->key]));
    }

    public function test_user_special_permission_has_higher_priority_than_group_inherited_permission()
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepo->create([
            'title' => 'Permission 1',
            'description' => 'some description',
            'key' => 'permission.1',
        ]);

        /** @var Group $group */
        $group = $this->groupRepo->create([
            'name' => 'Group 1',
            'permissions' => [],
        ]);

        $group->addPermission($permission,Permission::PERMISSION_ALLOW);

        /** @var User $user */
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
        ]);

        $user->groups()->attach($group);

        $user->addPermission($permission,Permission::PERMISSION_DENY);

        $this->assertFalse($user->hasPermission($permission->key));
        $this->assertFalse($user->hasAnyPermission([$permission->key]));
    }

    public function test_can_add_permission_to_group()
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepo->create([
            'title' => 'Permission 1',
            'description' => 'some description',
            'key' => 'permission.1',
        ]);

        /** @var Group $group */
        $group = $this->groupRepo->create([
            'name' => 'Group 1',
            'permissions' => [],
        ]);

        $group->addPermission($permission,Permission::PERMISSION_ALLOW);

        $this->assertTrue($group->hasPermission($permission->key));
    }

    public function test_can_remove_permission_to_group()
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepo->create([
            'title' => 'Permission 1',
            'description' => 'some description',
            'key' => 'permission.1',
        ]);

        /** @var Group $group */
        $group = $this->groupRepo->create([
            'name' => 'Group 1',
            'permissions' => [],
        ]);

        $group->addPermission($permission,Permission::PERMISSION_ALLOW);

        $this->assertTrue($group->hasPermission($permission->key));

        // now lets remove the permission
        $group->removePermission($permission->id);

        $this->assertFalse($group->hasPermission($permission->key));
    }

    public function test_user_have_permissions_from_group_and_special_permission()
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepo->create([
            'title' => 'Permission 1',
            'description' => 'some description',
            'key' => 'permission.1',
        ]);

        /** @var Group $group */
        $group = $this->groupRepo->create([
            'name' => 'Group 1',
            'permissions' => [],
        ]);

        $group->addPermission($permission,Permission::PERMISSION_ALLOW);

        /** @var User $user */
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
        ]);

        $user->groups()->attach($group);

        /** @var Permission $permission2 */
        $permission2 = $this->permissionRepo->create([
            'title' => 'Permission 2',
            'description' => 'some description',
            'key' => 'permission.2',
        ]);

        $user->addPermission($permission2,Permission::PERMISSION_ALLOW);

        $this->assertTrue($user->hasPermission($permission->key));
        $this->assertTrue($user->hasPermission($permission2->key));
    }
}