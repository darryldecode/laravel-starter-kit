<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 5:07 PM
 */

namespace App\Repositories;


use App\Contracts\PermissionRepository;
use App\Permission as PermissionModel;
use App\Utilities\Helpers;

class MySQLPermissionRepositoryRepository implements PermissionRepository
{
    /**
     * index items
     *
     * @param array $params
     * @return Result
     */
    public function index($params)
    {
        $title = Helpers::hasValue($params['title']);
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);

        $q = PermissionModel::with([])->orderBy($orderBy,$orderSort);

        (!$title) ?: $q = $q->where('title','like',"%{$title}%");

        if($paginate==='yes')
        {
            return new Result(true,Result::MESSAGE_SUCCESS_LIST,$q->paginate($perPage));
        }

        return new Result(true,Result::MESSAGE_SUCCESS_LIST,$q->get());
    }

    /**
     * create new item
     *
     * @param array $payload
     * @return Result
     */
    public function create($payload)
    {
        $Permission = PermissionModel::create([
           'title' => $payload['title'],
           'description' => $payload['description'],
           'permission' => $payload['permission'],
        ]);

        if(!$Permission) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        return new Result(true,Result::MESSAGE_SUCCESS_CREATE,$Permission,201);
    }

    /**
     * update item
     *
     * @param $payload
     * @return Result
     */
    public function update($payload)
    {
        $Permission = PermissionModel::find($payload['id']);

        if(!$Permission) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        (!$payload['title']) ?: $Permission->title = $payload['title'];
        (!$payload['description']) ?: $Permission->description = $payload['description'];
        (!$payload['permission']) ?: $Permission->permission = $payload['permission'];

        if(!$Permission->save()) return new Result(false,Result::MESSAGE_FAILED_UPDATE,null,400);

        return new Result(true,Result::MESSAGE_SUCCESS_UPDATE,$Permission,200);
    }

    /**
     * delete by id
     *
     * @param int $id
     * @return Result
     */
    public function delete($id)
    {
        $Permission = PermissionModel::find($id);

        if(!$Permission) return new Result(false,Result::MESSAGE_NOT_FOUND,null,400);

        $Permission->delete();

        return new Result(true,Result::MESSAGE_SUCCESS_DELETE,null,200);
    }

    /**
     * get resource by id
     *
     * @param $id
     * @return Result
     */
    public function get($id)
    {
        $permission = PermissionModel::find($id);

        if(!$permission) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        return new Result(true,Result::MESSAGE_SUCCESS,$permission,200);
    }
}