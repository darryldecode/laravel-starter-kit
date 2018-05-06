<?php

namespace App\Components\User\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 * @package App\Components\User\Models
 *
 * @property int $id
 * @property string $name
 * @property array $permissions
 * @property int $members_count
 */
class Group extends Model
{
    const SUPER_USER_GROUP_NAME = 'Super User';
    const DEFAULT_USER_GROUP_NAME = 'User';

    /**
     * The valid permission values
     * 1 means allow and 0 means deny
     *
     * @var array
     */
    static $validPermissionValues = array(1, -1);

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'permissions'];

    /**
     * the rules of the Group for validation before persisting
     *
     * @var array
     */
    public static $rules = array(
        'name' => 'required',
        'permissions' => 'array',
    );

    protected $appends = ['members_count'];

    /**
     * get validation rules
     *
     * @return array
     */
    public function getValidationRules()
    {
        return self::$rules;
    }

    /**
     * serializes permission attribute on the fly before saving to database
     *
     * @param $permissions
     */
    public function setPermissionsAttribute($permissions)
    {
        $this->attributes['permissions'] = serialize($permissions);
    }

    /**
     * unserializes permissions attribute before spitting out from database
     *
     * @return mixed
     */
    public function getPermissionsAttribute()
    {
        return unserialize($this->attributes['permissions']);
    }

    /**
     * the total number of users under this group
     *
     * @return mixed
     */
    public function getMembersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * returns the users on this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class,'user_group_pivot_table','group_id');
    }

    /**
     * adds a new permission or if permission already exist, just update the value
     *
     * @param int|Permission $permission
     * @param int $value the permission value (allow=1, inherit=0, deny=-1)
     * @return bool
     */
    public function addPermission($permission, $value)
    {
        $userCurrentPermissions = $this->permissions;
        $updateOnly = false;

        // maybe a permission ID
        if(is_int($permission))
        {
            $Permission = Permission::find($permission);

            if(!$Permission) return false;

            // loop through current permissions if already exist,
            // if so, we will just update the value
            foreach ($userCurrentPermissions as $index => $p)
            {
                if($p['key'] == $Permission->key)
                {
                    $updateOnly = true;
                    $userCurrentPermissions[$index]['value'] = $value;
                }
            }

            // if not found yet, lets add it
            if(!$updateOnly)
            {
                $userCurrentPermissions[] = [
                    'key' => $Permission->key,
                    'title' => $Permission->title,
                    'description' => $Permission->description,
                    'value' => $value,
                ];
            }
        }

        // maybe a permission object
        elseif ($permission instanceof Permission)
        {
            // loop through current permissions if already exist,
            // if so, we will just update the value
            foreach ($userCurrentPermissions as $index => $p)
            {
                if($p['key'] == $permission->key)
                {
                    $updateOnly = true;
                    $userCurrentPermissions[$index]['value'] = $value;
                }
            }

            // if not found yet, lets add it
            if(!$updateOnly)
            {
                $userCurrentPermissions[] = [
                    'key' => $permission->key,
                    'title' => $permission->title,
                    'description' => $permission->description,
                    'value' => $value,
                ];
            }
        }

        // invalid
        else
        {
            return false;
        }

        // assign the new permissions value
        $this->permissions = $userCurrentPermissions;

        return $this->save();
    }

    /**
     * @param int|Permission $permission
     * @return bool
     */
    public function removePermission($permission)
    {
        $userCurrentPermissions = $this->permissions;

        if(is_int($permission))
        {
            $Permission = Permission::find($permission);

            if(!$Permission) return false;

            foreach ($userCurrentPermissions as $index => $p)
            {
                if($p['key'] == $Permission->key) unset($userCurrentPermissions[$index]);
            }
        }

        elseif ($permission instanceof Permission)
        {
            foreach ($userCurrentPermissions as $index => $p)
            {
                if($p['key'] == $permission->key) unset($userCurrentPermissions[$index]);
            }
        }

        else
        {
            return false;
        }

        // re-index
        array_values($userCurrentPermissions);

        // assign new values
        $this->permissions = $userCurrentPermissions;

        return $this->save();
    }

    /**
     * check if a group has permission
     *
     * @param string $permissionKey
     * @return bool
     */
    public function hasPermission($permissionKey)
    {
        $has = false;

        foreach ($this->permissions as $index => $p)
        {
            if($p['key'] == $permissionKey && $p['value'] == Permission::PERMISSION_ALLOW) $has = true;
        }

        return $has;
    }
}
