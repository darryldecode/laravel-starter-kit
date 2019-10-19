<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/9/2017
 * Time: 10:02 PM
 */

namespace App\Components\File\Repositories;


use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\File\Models\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class FileRepository extends BaseRepository
{
    public function __construct(File $model)
    {
        parent::__construct($model);
    }

    /**
     * @author darryldecode <darrylfernandez.com>
     * @since  v1.0
     *
     * @param $id
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteRecordAndFile($id)
    {
        /** @var File $fileRecord */
        $fileRecord = $this->model->find($id);

        // delete the actual file
        Storage::delete($fileRecord->path);

        // delete record
        $fileRecord->delete();

        return true;
    }

    /**
     * list resource
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function index($params)
    {
        return $this->get($params,['user','group'],function($q) use ($params)
        {
            $groupIds = explode(',',Arr::get($params,'file_group_id',''));
            $name = Arr::get($params,'name',null);

            if($name) $q->where('name','like',"%{$name}%");
            if(count($groupIds) > 0 && !empty($groupIds[0])) $q->whereIn('file_group_id',$groupIds);

            return $q;
        });
    }
}