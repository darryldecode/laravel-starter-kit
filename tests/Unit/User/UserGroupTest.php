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
use App\Components\User\Models\User;
use App\Components\User\Repositories\GroupRepository;
use App\Components\User\Repositories\UserRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class UserGroupTest extends TestCase
{
    /**
     * @var UserRepository
     */
    protected $userRepo;

    /**
     * @var GroupRepository
     */
    protected $groupRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepo = new UserRepository(new User());
        $this->groupRepo = new GroupRepository(new Group());
    }

    public function test_it_can_assign_user_to_a_group()
    {
        /** @var Group $group */
        $group = $this->groupRepo->create([
            'name' => 'group 1',
            'permissions' => []
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

        $this->assertTrue($user->inGroup($group));
        $this->assertTrue($user->inGroup($group->name));
        $this->assertTrue($user->inGroup($group->id));
    }

    public function test_user_should_not_be_in_group()
    {
        /** @var Group $group */
        $group = $this->groupRepo->create([
            'name' => 'group 1',
            'permissions' => []
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

        // attach
        $user->groups()->attach($group);

        $this->assertTrue($user->inGroup($group));
        $this->assertTrue($user->inGroup($group->name));
        $this->assertTrue($user->inGroup($group->id));

        // detach
        $user->groups()->detach($group);

        $this->assertFalse($user->inGroup($group));
        $this->assertFalse($user->inGroup($group->name));
        $this->assertFalse($user->inGroup($group->id));
    }
}