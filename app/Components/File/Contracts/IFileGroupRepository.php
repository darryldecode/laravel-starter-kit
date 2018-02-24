<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/9/2017
 * Time: 10:09 PM
 */

namespace App\Components\File\Contracts;



use App\Components\Core\Result;

interface IFileGroupRepository
{
    /**
     * list resource
     *
     * @param array $params
     * @return Result
     */
    public function index($params);

    /**
     * create resource
     *
     * @param array $data
     * @return Result
     */
    public function create($data);

    /**
     * get resource by id
     *
     * @param $id
     * @return Result
     */
    public function get($id);

    /**
     * update resource
     *
     * @param int $id
     * @param array $data
     * @return Result
     */
    public function update($id, $data);

    /**
     * delete resource by id
     *
     * @param int $id
     * @return Result
     */
    public function delete($id);
}