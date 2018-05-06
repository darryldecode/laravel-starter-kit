<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 4:58 PM
 */

namespace App\Components\User\Repositories;


use App\Components\Core\BaseRepository;
use App\Components\User\Models\Group;
use App\Components\Core\Utilities\Helpers;

class GroupRepository extends BaseRepository
{
    public function __construct(Group $model)
    {
        parent::__construct($model);
    }

    /**
     * index items
     *
     * @param array $params
     * @return GroupRepository[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function index($params)
    {
        $with = Helpers::hasValue($params['with'],[]);
        $name = Helpers::hasValue($params['name'],null);
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);

        $q = $this->model->with(array_merge(['users'],$with))->orderBy($orderBy,$orderSort);

        (!$name) ?: $q = $q->where('name','like',"%{$name}%");

        if($paginate==='yes')
        {
            return $q->paginate($perPage);
        }

        return $q->get();
    }

    /**
     * delete by id
     *
     * @param int $id
     * @return boolean
     * @throws \Exception
     */
    public function delete(int $id)
    {
        $ids = explode(',',$id);

        foreach ($ids as $id)
        {
            /** @var Group $Group */
            $Group = $this->model->find($id);

            if(!$Group) return false;

            $Group->users()->detach();
            $Group->delete();
        }

        return true;
    }
}