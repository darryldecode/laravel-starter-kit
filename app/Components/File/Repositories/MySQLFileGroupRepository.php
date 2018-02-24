<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/10/2017
 * Time: 8:29 PM
 */

namespace App\Components\File\Repositories;


use App\Components\Core\Result;
use App\Components\Core\Utilities\Helpers;
use App\Components\File\Contracts\IFileGroupRepository;
use App\Components\File\Models\FileGroup;

class MySQLFileGroupRepository implements IFileGroupRepository
{

    /**
     * list resource
     *
     * @param array $params
     * @return Result
     */
    public function index($params)
    {
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $name = Helpers::hasValue($params['name'],null);
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);

        $q = FileGroup::with(['files'])->orderBy($orderBy,$orderSort);

        if($name) $q = $q->where('name','like',"%{$name}%");

        if($paginate==='yes')
        {
            return new Result(true,Result::MESSAGE_SUCCESS_LIST,$q->paginate($perPage));
        }

        return new Result(true,Result::MESSAGE_SUCCESS_LIST,$q->get());
    }

    /**
     * create resource
     *
     * @param array $data
     * @return Result
     */
    public function create($data)
    {
        $res = FileGroup::create([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        if(!$res) return new Result(false,Result::MESSAGE_FAILED_CREATE,null,400);

        return new Result(true,Result::MESSAGE_SUCCESS_CREATE,$res,201);
    }

    /**
     * get resource by id
     *
     * @param $id
     * @return Result
     */
    public function get($id)
    {
        $res = FileGroup::with(['files'])->find($id);

        if(!$res) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        return new Result(true,Result::MESSAGE_SUCCESS,$res,200);
    }

    /**
     * update resource
     *
     * @param int $id
     * @param array $data
     * @return Result
     */
    public function update($id, $data)
    {
        $FileGroup = FileGroup::find($id);

        if(!$FileGroup) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        $FileGroup->name = $data['name'];
        $FileGroup->description = $data['description'];

        if(!$FileGroup->save()) return new Result(false,Result::MESSAGE_FAILED_UPDATE,null,400);

        return new Result(true,Result::MESSAGE_SUCCESS_CREATE,$FileGroup,201);
    }

    /**
     * delete resource by id
     *
     * @param int $id
     * @return Result
     */
    public function delete($id)
    {
        $FileGroup = FileGroup::find($id);

        $FileGroup->delete();

        return new Result(true,Result::MESSAGE_SUCCESS_DELETE,null,200);
    }
}