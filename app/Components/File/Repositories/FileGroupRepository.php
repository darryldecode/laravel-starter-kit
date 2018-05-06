<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/10/2017
 * Time: 8:29 PM
 */

namespace App\Components\File\Repositories;


use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\File\Models\FileGroup;

class FileGroupRepository extends BaseRepository
{
    public function __construct(FileGroup $model)
    {
        parent::__construct($model);
    }

    /**
     * list resource
     *
     * @param array $params
     * @return FileGroupRepository[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function index($params)
    {
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $name = Helpers::hasValue($params['name'],null);
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);

        $q = $this->model->with(['files'])->orderBy($orderBy,$orderSort);

        if($name) $q = $q->where('name','like',"%{$name}%");

        if($paginate==='yes')
        {
            return $q->paginate($perPage);
        }

        return $q->get();
    }
}