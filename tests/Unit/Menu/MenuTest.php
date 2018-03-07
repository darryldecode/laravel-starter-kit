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
use App\Components\User\Repositories\MySQLUserRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class MenuTest extends TestCase
{
    /**
     * @var MySQLUserRepository
     */
    protected $userRepo;

    public function setUp()
    {
        parent::setUp();
        $this->userRepo = new MySQLUserRepository();
    }

    public function test_menu_item_should_not_show_if_user_dont_have_permission()
    {
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [
                ['permission'=> 'superuser', 'value'=> User::PERMISSION_DENY]
            ],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
            'groups' => []
        ])->getData();

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
        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [
                ['permission'=> 'superuser', 'value'=> User::PERMISSION_ALLOW]
            ],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
            'groups' => []
        ])->getData();

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

        $this->assertTrue($menuManager->hasMenu('Super User Dashboard'));
    }

    public function test_menu_item_should_not_show_if_user_is_not_in_group()
    {
        $group1 = factory(Group::class)->create();

        $user = $this->userRepo->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => '12345678', // hash on the fly
            'permissions' => [],
            'active' => null,
            'activation_key' => (Uuid::uuid4())->toString(),
            'groups' => [
                $group1->id => false
            ]
        ])->getData();

        $menuManager = new MenuManager();
        $menuManager->setUser($user);
        $menuManager->addMenu(new MenuItem([
            'group_requirements' => [$group1->name],
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
        $group1 = factory(Group::class)->create();
        $user = factory(User::class)->create();
        $user->groups()->attach($group1);

        $menuManager = new MenuManager();
        $menuManager->setUser($user);
        $menuManager->addMenu(new MenuItem([
            'group_requirements' => [$group1->name],
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
        $permission = factory(Permission::class)->create();
        $group1 = factory(Group::class)->create();
        $user = factory(User::class)->create([
            'permissions' => [
                ['permission'=>$permission->permission, 'value'=>User::PERMISSION_DENY]
            ]
        ]);
        $user->groups()->attach($group1);

        $menuManager = new MenuManager();
        $menuManager->setUser($user);
        $menuManager->addMenu(new MenuItem([
            'group_requirements' => [$group1->name],
            'permission_requirements' => [$permission->permission],
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
        $permission = factory(Permission::class)->create();
        $group1 = factory(Group::class)->create();
        $user = factory(User::class)->create([
            'permissions' => [
                ['permission'=>$permission->permission, 'value'=>User::PERMISSION_ALLOW]
            ]
        ]);

        $menuManager = new MenuManager();
        $menuManager->setUser($user);
        $menuManager->addMenu(new MenuItem([
            'group_requirements' => [$group1->name],
            'permission_requirements' => [$permission->permission],
            'label'=>'Group 1 Nav',
            'nav_type' => MenuItem::$NAV_TYPE_NAV,
            'icon'=>'dashboard',
            'route_type'=>'vue',
            'route_name'=>'dashboard'
        ]));

        $this->assertFalse($menuManager->hasMenu('Group 1 Nav'));
    }
}