<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 6:37 AM
 */

namespace App\Components\User\Repositories;


use App\Components\Core\BaseRepository;
use App\Components\User\Models\User;
use App\Components\Core\Utilities\Helpers;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * list all users
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function listUsers($params)
    {
        $email = Helpers::hasValue($params['email']);
        $name = Helpers::hasValue($params['name']);
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);
        $groupId = Helpers::hasValue($params['group_id'],"");

        $q = $this->model->with(['groups'])
            ->orderBy($orderBy,$orderSort);

        $q->ofGroups(Helpers::commasToArray($groupId));
        $q->ofName($name);
        $q->ofEmail($email);

        if($paginate==='yes')
        {
            return $q->paginate($perPage);
        }

        return $q->get();
    }

    /**
     * delete a user by id
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id)
    {
        $ids = explode(',',$id);

        foreach ($ids as $id)
        {
            /** @var User $User */
            $User = $this->model->find($id);

            if(!$User)
            {
                return false;
            };

            $User->groups()->detach();
            $User->delete();
        }

        return true;
    }
}