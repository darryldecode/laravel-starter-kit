<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 6:38 AM
 */

namespace App\Contracts;


use App\Repositories\Result;
use App\User;

interface UserRepository
{
    /**
     * list all users
     *
     * @param array $params
     * @return Result
     */
    public function listUsers($params);

    /**
     * get resource by id
     *
     * @param $id
     * @return Result
     */
    public function get($id);

    /**
     * create new user
     *
     * @param array $payload
     * @return Result
     */
    public function create($payload);

    /**
     * update user
     *
     * @param int $id
     * @param array $payload
     * @return Result
     */
    public function update($id, $payload);

    /**
     * delete a user by id
     *
     * @param int $id
     * @return Result
     */
    public function delete($id);
}