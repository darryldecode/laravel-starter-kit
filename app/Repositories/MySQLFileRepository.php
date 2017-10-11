<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/9/2017
 * Time: 10:02 PM
 */

namespace App\Repositories;


use App\Contracts\FileRepository;
use App\File;
use App\FileGroup;
use App\Utilities\Helpers;
use Illuminate\Http\Request;

class MySQLFileRepository implements FileRepository
{

    /**
     * list resource
     *
     * @param array $params
     * @return Result
     */
    public function index($params)
    {
        // we need to transform group ids to array: 1,2,3,4 => [1,2,3,4]
        $groupIds = explode(',',Helpers::hasValue($params['file_group_id'],''));
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $name = Helpers::hasValue($params['name'],null);
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);

        $q = File::with(['user','group'])->orderBy($orderBy,$orderSort);

        if($name) $q = $q->where('name','like',"%{$name}%");
        if(count($groupIds) > 0) $q = $q->whereIn('file_group_id',$groupIds);

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
        $File = File::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'uploaded_by' => $data['uploaded_by'],
            'file_group_id' => $data['file_group_id'],
            'file_type' => $data['file_type'],
            'extension' => $data['extension'],
            'size' => $data['size'],
            'path' => $data['path'],
        ]);

        if(!$File) return new Result(false,Result::MESSAGE_FAILED_CREATE,null,400);

        return new Result(true,Result::MESSAGE_SUCCESS_CREATE,$File,201);
    }

    /**
     * get resource by id
     *
     * @param $id
     * @return Result
     */
    public function get($id)
    {
        $File = File::find($id);

        if(!$File) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        return new Result(true,Result::MESSAGE_SUCCESS,$File,200);
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
        $File = File::find($id);

        if(!$File) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        $File->name = $data['name'];
        $File->description = $data['description'];
        $File->uploaded_by = $data['uploaded_by'];
        $File->file_group_id = $data['file_group_id'];
        $File->file_type = $data['file_type'];
        $File->extension = $data['extension'];
        $File->size = $data['size'];
        $File->path = $data['path'];

        if(!$File->save()) return new Result(false,Result::MESSAGE_FAILED_UPDATE,null,400);

        return new Result(true,Result::MESSAGE_SUCCESS_UPDATE,$File,201);
    }

    /**
     * delete resource by id
     *
     * @param int $id
     * @return Result
     */
    public function delete($id)
    {
        $File = File::find($id);

        if(!$File) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        $File->delete();

        return new Result(true,Result::MESSAGE_SUCCESS,$File,200);
    }

    /**
     * @param Request $request
     * @return Result
     */
    public function upload($request)
    {
        $file = $request->file('file');

        $filePath = $file->store('local');

        if(!$filePath) return new Result(false,"Failed to upload.",null,400);

        return new Result(true,'Upload success.',$filePath,200);
    }
}