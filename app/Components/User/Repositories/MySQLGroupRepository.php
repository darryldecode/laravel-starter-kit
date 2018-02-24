<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 4:58 PM
 */

namespace App\Components\User\Repositories;


use App\Components\Core\Result;
use App\Components\User\Contracts\IGroupRepository;
use App\Components\User\Models\Group;
use App\Components\Core\Utilities\Helpers;

class MySQLGroupRepository implements IGroupRepository
{
    /**
     * index items
     *
     * @param array $params
     * @return Result
     */
    public function index($params)
    {
        $with = Helpers::hasValue($params['with'],[]);
        $name = Helpers::hasValue($params['name'],null);
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);

        $q = Group::with(array_merge(['users'],$with))->orderBy($orderBy,$orderSort);

        (!$name) ?: $q = $q->where('name','like',"%{$name}%");

        if($paginate==='yes')
        {
            return new Result(true,'list groups',$q->paginate($perPage));
        }

        return new Result(true,'list groups',$q->get());
    }

    /**
     * create new item
     *
     * @param array $payload
     * @return Result
     */
    public function create($payload)
    {
        $Group = Group::create([
            'name' => $payload['name'],
            'permissions' => $payload['permissions'],
        ]);

        if(!$Group) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        return new Result(true,'group created',$Group,201);
    }

    /**
     * update item
     *
     * @param int $id
     * @param array $payload
     * @return Result
     */
    public function update($id, $payload)
    {
        $Group = Group::find($id);

        if(!$Group) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        $Group->name = $payload['name'];
        $Group->permissions = $payload['permissions'];

        if(!$Group->save()) return new Result(false,Result::MESSAGE_FAILED_UPDATE,null,400);

        return new Result(true,Result::MESSAGE_SUCCESS_UPDATE,$Group,200);
    }

    /**
     * delete by id
     *
     * @param int $id
     * @return Result
     */
    public function delete($id)
    {
        $ids = explode(',',$id);

        foreach ($ids as $id)
        {
            $Group = Group::find($id);

            if(!$Group) return new Result(false,"Failed to delete resource with id: {$id}. Error: ".Result::MESSAGE_NOT_FOUND,null,404);

            $Group->users()->detach();
            $Group->delete();
        }

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
        $Group = Group::find($id);

        if(!$Group) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        return new Result(true,Result::MESSAGE_SUCCESS,$Group,200);
    }
}