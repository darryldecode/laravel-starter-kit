<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 6:37 AM
 */

namespace App\Components\User\Repositories;


use App\Components\Core\Result;
use App\Components\User\Contracts\IUserRepository;
use App\Components\User\Models\User;
use App\Components\Core\Utilities\Helpers;
use Ramsey\Uuid\Uuid;

class MySQLUserRepository implements IUserRepository
{
    /**
     * list all users
     *
     * @param array $params
     * @return Result
     */
    public function listUsers($params)
    {
        $email = Helpers::hasValue($params['email']);
        $name = Helpers::hasValue($params['name']);
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);

        $q = User::with([])->orderBy($orderBy,$orderSort);

        (!$email) ?: $q = $q->where('email','like',"%{$email}%");
        (!$name) ?: $q = $q->where('name','like',"%{$name}%");

        if($paginate==='yes')
        {
            return new Result(true,Result::MESSAGE_SUCCESS_LIST,$q->paginate($perPage));
        }

        return new Result(true,Result::MESSAGE_SUCCESS_LIST,$q->get());
    }

    /**
     * create new user
     *
     * @param array $payload
     * @return Result
     */
    public function create($payload)
    {
        // create the user
        $User = User::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => $payload['password'], // hash on the fly
            'permissions' => $payload['permissions'],
            'active' => $payload['active'],
            'activation_key' => (Uuid::uuid4())->toString(),
        ]);

        if(!$User) return new Result(false,'User not found.',null, 404);

        // add the group
        if(Helpers::hasValue($payload['groups']) && count($payload['groups']) > 0)
        {
            foreach ($payload['groups'] as $groupId => $shouldAttach)
            {
                if($shouldAttach) $User->groups()->attach($groupId);
            }
        }

        return new Result(true,'User created.',$User,201);
    }

    /**
     * update user
     *
     * @param int $id
     * @param array $payload
     * @return Result
     */
    public function update($id, $payload)
    {
        $User = User::find($id);

        if(!$User) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        if(!$User->update($payload)) return new Result(false,'Failed to update.',null,400);

        // detach all group first
        $User->groups()->detach();

        // re attach needed
        if(Helpers::hasValue($payload['groups']) && count($payload['groups']) > 0)
        {
            foreach ($payload['groups'] as $groupId => $shouldAttach) {
                if ($shouldAttach) $User->groups()->attach($groupId);
            }
        }

        return new Result(true,'update success',$User,200);
    }

    /**
     * delete a user by id
     *
     * @param int $id
     * @return Result
     */
    public function delete($id)
    {
        $ids = explode(',',$id);

        foreach ($ids as $id)
        {
            $User = User::find($id);

            if(!$User)
            {
                return new Result(false,"Failed to delete resource with id: {$id}. Error: ".Result::MESSAGE_NOT_FOUND,null,404);
            };

            $User->groups()->detach();
            $User->delete();
        }

        return new Result(true,Result::MESSAGE_SUCCESS_DELETE,null);
    }

    /**
     * get resource by id
     *
     * @param $id
     * @return Result
     */
    public function get($id)
    {
        $User = User::with(['groups'])->find($id);

        if(!$User) return new Result(false,'user not found',null,404);

        return new Result(true,Result::MESSAGE_SUCCESS,$User,200);
    }
}