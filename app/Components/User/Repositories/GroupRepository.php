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
use Illuminate\Support\Arr;

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
        return $this->get($params,[],function($q) use ($params)
        {
            $name = Arr::get($params,'name','');

            $q->where('name','like',"%{$name}%");

            return $q;
        });
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