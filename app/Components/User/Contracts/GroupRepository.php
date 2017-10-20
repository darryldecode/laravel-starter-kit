<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 4:52 PM
 */

namespace App\Components\User\Contracts;


use App\Repositories\Result;

interface GroupRepository
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
     * @param int $id
     * @param array $payload
     * @return Result
     */
    public function update($id, $payload);

    /**
     * delete by id
     *
     * @param int|string $id
     * @return Result
     */
    public function delete($id);
}