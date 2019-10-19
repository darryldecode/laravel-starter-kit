<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 5:07 PM
 */

namespace App\Components\User\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\User\Models\Permission;
use Illuminate\Support\Arr;

class PermissionRepository extends BaseRepository
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    /**
     * index items
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Model[]|\Illuminate\Support\Collection
     */
    public function index($params)
    {
        return $this->get($params,[],function($q) use ($params)
        {
            $title = Arr::get($params,'title','');

            $q->where('title','like',"%{$title}%");

            return $q;
        });
    }
}