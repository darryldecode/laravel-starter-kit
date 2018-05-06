<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 5/6/2018
 * Time: 11:20 AM
 */

namespace App\Components\Core;


use Illuminate\Database\Eloquent\Model;

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
     */
    public function delete(int $id)
    {
        return $this->model->delete($id);
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