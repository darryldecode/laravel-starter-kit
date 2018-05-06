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

class FileRepository extends BaseRepository
{
    public function __construct(File $model)
    {
        parent::__construct($model);
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
            $groupIds = explode(',',array_get($params,'file_group_id',''));
            $name = array_get($params,'name',null);

            if($name) $q->where('name','like',"%{$name}%");
            if(count($groupIds) > 0 && !empty($groupIds[0])) $q->whereIn('file_group_id',$groupIds);

            return $q;
        });
    }
}