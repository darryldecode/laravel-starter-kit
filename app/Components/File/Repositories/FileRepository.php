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
        // we need to transform group ids to array: 1,2,3,4 => [1,2,3,4]
        $groupIds = explode(',',Helpers::hasValue($params['file_group_id'],''));
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $name = Helpers::hasValue($params['name'],null);
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);

        $q = $this->model->with(['user','group'])->orderBy($orderBy,$orderSort);

        if($name) $q->where('name','like',"%{$name}%");
        if(count($groupIds) > 0 && !empty($groupIds[0])) $q->whereIn('file_group_id',$groupIds);

        if($paginate==='yes')
        {
            return $q->paginate($perPage);
        }

        return $q->get();
    }
}