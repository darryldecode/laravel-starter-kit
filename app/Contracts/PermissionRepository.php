<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 4:53 PM
 */

namespace App\Contracts;


use App\Repositories\Result;

interface PermissionRepository
{
    /**
     * index items
     *
     * @param array $params
     * @return Result
     */
    public function index($params);

    /**
     * get resource by id
     *
     * @param $id
     * @return Result
     */
    public function get($id);

    /**
     * create new item
     *
     * @param array $payload
     * @return Result
     */
    public function create($payload);

    /**
     * update item
     *
     * @param $payload
     * @return Result
     */
    public function update($payload);

    /**
     * delete by id
     *
     * @param int $id
     * @return Result
     */
    public function delete($id);
}