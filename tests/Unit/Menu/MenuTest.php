<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 3/7/2018
 * Time: 11:07 AM
 */

namespace Tests\Unit\Menu;


use App\Components\Core\Menu\MenuItem;
use App\Components\Core\Menu\MenuManager;
use App\Components\User\Models\Group;
use App\Components\User\Models\Permission;
use App\Components\User\Models\User;
use App\Components\User\Repositories\GroupRepository;
use App\Components\User\Repositories\PermissionRepository;
use App\Components\User\Repositories\UserRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class MenuTest extends TestCase
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

    public function test_menu_item_should_not_show_if_user_dont_have_permission()
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

        $menuManager = new MenuManager();
        $menuManager->setUser($user);
        $menuManager->addMenu(new MenuItem([
            'group_requirements' => [],
            'permission_requirements' => ['superuser'],
            'label'=>'Super User Dashboard',
            'nav_type' => MenuItem::$NAV_TYPE_NAV,
            'icon'=>'dashboard',
            'route_type'=>'vue',
            'route_name'=>'dashboard'
        ]));

        $this->assertFalse($menuManager->hasMenu('Super User Dashboard'));
    }

    public function test_menu_item_should_show_if_user_have_permission()
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
        ]);

        $user->addPermission($permission,Permission::PERMISSION_ALLOW);

        $menuManager = new MenuManager();
        $menuManager->setUser($user);
        $menuManager->addMenu(new MenuItem([
            'group_requirements' => [],
            'permission_requirements' => ['permission.1'],
            'label'=>'Super User Dashboard',
            'nav_type' => MenuItem::$NAV_TYPE_NAV,
            'icon'=>'dashboard',
            'route_type'=>'vue',
            'route_name'=>'dashboard'
        ]));

        $this->assertTrue($menuManager->hasMenu('Super User Dashboard'));
    }

    public function test_menu_item_should_not_show_if_user_is_not_in_group()
    {
        /** @var Group $group */
        $group = $this->groupRepo->create([
            'name' => 'Group 1',
            'permissions' => [],
        ]);

        /** @var User $user */
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
        ]);

        $menuManager = new MenuManager();
        $menuManager->setUser($user);
        $menuManager->addMenu(new MenuItem([
            'group_requirements' => [$group->name],
            'permission_requirements' => [],
            'label'=>'Group 1 Nav',
            'nav_type' => MenuItem::$NAV_TYPE_NAV,
            'icon'=>'dashboard',
            'route_type'=>'vue',
            'route_name'=>'dashboard'
        ]));

        $this->assertFalse($menuManager->hasMenu('Group 1 Nav'));
    }

    public function test_menu_item_should_show_if_user_is_in_group()
    {
        /** @var Group $group */
        $group = $this->groupRepo->create([
            'name' => 'Group 1',
            'permissions' => [],
        ]);

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

        $menuManager = new MenuManager();
        $menuManager->setUser($user);
        $menuManager->addMenu(new MenuItem([
            'group_requirements' => [$group->name],
            'permission_requirements' => [],
            'label'=>'Group 1 Nav',
            'nav_type' => MenuItem::$NAV_TYPE_NAV,
            'icon'=>'dashboard',
            'route_type'=>'vue',
            'route_name'=>'dashboard'
        ]));

        $this->assertTrue($menuManager->hasMenu('Group 1 Nav'));
    }

    public function test_menu_item_should_not_show_if_user_is_in_group_but_dont_have_permission()
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

        // add permission to group
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

        $menuManager = new MenuManager();
        $menuManager->setUser($user);
        $menuManager->addMenu(new MenuItem([
            'group_requirements' => [$group->name],
            'permission_requirements' => [$permission->key],
            'label'=>'Group 1 Nav',
            'nav_type' => MenuItem::$NAV_TYPE_NAV,
            'icon'=>'dashboard',
            'route_type'=>'vue',
            'route_name'=>'dashboard'
        ]));

        $this->assertFalse($menuManager->hasMenu('Group 1 Nav'));
    }

    public function test_menu_item_should_not_show_if_user_is_not_in_group_and_have_permission()
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

        /** @var User $user */
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
        ]);

        $user->addPermission($permission,Permission::PERMISSION_ALLOW);

        $menuManager = new MenuManager();
        $menuManager->setUser($user);
        $menuManager->addMenu(new MenuItem([
            'group_requirements' => [$group->name],
            'permission_requirements' => [$permission->key],
            'label'=>'Group 1 Nav',
            'nav_type' => MenuItem::$NAV_TYPE_NAV,
            'icon'=>'dashboard',
            'route_type'=>'vue',
            'route_name'=>'dashboard'
        ]));

        $this->assertFalse($menuManager->hasMenu('Group 1 Nav'));
    }
}