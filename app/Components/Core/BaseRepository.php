<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 5/6/2018
 * Time: 11:20 AM
 */

namespace App\Components\Core;


use App\Components\Core\Utilities\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    /**
     * @var static|mixed Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @param array $params
     * @param array $with
     * @param callable|null $callable
     * @return LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function get($params = [], $with = [], $callable = null)
    {
        $q = $this->model->with($with);

        $q->orderBy($params['order_by'] ?? 'id', $params['order_sort'] ?? 'desc');

        // call the function if provided
        if(!is_null($callable)) $q = call_user_func_array($callable,[&$q]);

        // if per page is -1, we don't need to paginate at all, but we still return the paginated
        // data structure to our response. Let's just put the biggest number we can imagine.
        if(Helpers::hasValue($params['per_page']) && ($params['per_page']==-1)) $params['per_page'] = 999999999999;

        // if don't want any pagination
        if(Helpers::hasValue($params['paginate']) && ($params['paginate']=='no')) return $q->get();

        return $q->paginate($params['per_page'] ?? 10);
    }

    /**
     * @param array $data
     * @return Model|boolean
     */
    public function create(array $data = [])
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes)
    {
        $model = $this->find($id);

        if(!$model) return false;

        return $model->update($attributes);
    }

    /**
     * @param int $id
     * @return bool|null
     * @throws \Exception
     */
    public function delete(int $id)
    {
        return $this->model->find($id)->delete();
    }

    /**
     * @param int $id
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|Model[]|null|static
     */
    public function find(int $id, $with = [])
    {
        return $this->model->with($with)->find($id);
    }

    /**
     * @param int $id
     * @return mixed|Model|boolean
     */
    public function findWithTrash(int $id)
    {
        return $this->model->withTrashed()->find($id);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return mixed|Model|static
     */
    public function findBy($field, $value)
    {
        return $this->model->where($field,$value)->first();
    }
}