<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 5/27/2017
 * Time: 7:55 AM
 */

namespace App\Components\User\Models;
use Hash;
use Illuminate\Support\Arr;

/**
 * Class UserTrait
 * @package App\SOLAR\User\Models
 */
trait UserTrait
{
    /**
     * hashes password
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
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
        if(empty($this->attributes['permissions']) || is_null($this->attributes['permissions'])) return [];

        return unserialize($this->attributes['permissions']);
    }

    /**
     * returns the groups of the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class,'user_group_pivot_table','user_id');
    }

    /**
     * the users meta
     *
     * @return mixed
     */
    public function meta()
    {
        return $this->hasMany(UserMeta::class,'user_id');
    }

    /**
     * just a helper to parse a meta value from an array to object properties
     * so it will be easy to deal in UI portion
     *
     * @return \StdClass
     */
    public function getParseMeta()
    {
        $metaClass = new \StdClass();

        // we will iterate through all of its meta relations
        // and put it as a property so it will be easy in front end
        $this->meta()->each(function($meta) use (&$metaClass)
        {
            $metaClass->{$meta['key']} = $meta['value'];
        });

        // we will check if the declared meta data key
        // already exist in the $metaClass as property, if not
        // we will declare it and put value null as default
        foreach (UserMeta::getKeys() as $k => $v)
        {
            if(!property_exists($metaClass,$v['key'])) $metaClass->{$v['key']} = null;
        }

        return $metaClass;
    }

    /**
     * check if the user is superuser
     *
     * @return bool
     */
    public function isSuperUser()
    {
        return $this->hasPermission('superuser');
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
     * check if user has permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        $userCombinedPermissions = $this->getCombinedPermissions();

        $superUser = Arr::get($userCombinedPermissions, 'superuser');

        if( $superUser === Permission::PERMISSION_ALLOW ) return true;

        $permissionValue = Arr::get($userCombinedPermissions, $permission);

        return $permissionValue === Permission::PERMISSION_ALLOW;
    }

    /**
     * check if has any permissions
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions)
    {
        if( $this->isSuperUser() ) return true;

        $hasPermission = false;

        foreach($permissions as $permission)
        {
            if( $this->hasPermission($permission) )
            {
                $hasPermission = true;
            }
        }

        return $hasPermission;
    }

    /**
     * check if user is in a group
     *
     * @param $group
     * @return bool
     */
    public function inGroup($group)
    {
        $found = false;

        if( is_string($group) )
        {
            $this->groups()->each(function($g) use ($group, &$found)
            {
                if( $g->name == $group )
                {
                    $found = true;
                }
            });

            return $found;
        }
        else if ( is_int($group) )
        {
            $this->groups()->each(function($g) use ($group, &$found)
            {
                if( (int)$g->id == $group )
                {
                    $found = true;
                }
            });

            return $found;
        }
        else if ( is_object($group) )
        {
            $this->groups()->each(function($g) use ($group, &$found)
            {
                if( $g->name == $group->name )
                {
                    $found = true;
                }
            });

            return $found;
        }
        else
        {
            return $found;
        }
    }

    /**
     * check if a user is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active !== null;
    }

    /**
     * the over all permissions of the user
     *
     * @return array
     */
    public function getCombinedPermissions()
    {
        // the user group permissions, if user has many groups, it will combine all the groups permissions
        $groupPermissions = $this->getGroupPermissions();

        // if the user is a super user, give the user all the permissions
        if($this->inGroup(Group::SUPER_USER_GROUP_NAME))
        {
            $availablePermissions = Permission::all();

            $allPermissions = [];

            $availablePermissions->each(function($p) use (&$allPermissions)
            {
                $allPermissions[$p->key] = 1;
            });

            return array_merge($allPermissions, $groupPermissions);
        }

        // the user specific assigned permissions
        $userSpecificPermissions = $this->getSpecialPermissions();

        foreach($userSpecificPermissions as $uPermission => $uValue)
        {
            // if the permission is inherit
            if( $uValue == Permission::PERMISSION_INHERIT )
            {
                // we will check if this permission exists in his group permissions,
                // if so, we will get the value from that group permissions and we will use it as its value
                // if it does not exist on its group permissions, just deny it
                if( array_key_exists($uPermission, $groupPermissions) )
                {
                    $userSpecificPermissions[$uPermission] = $groupPermissions[$uPermission];
                    unset($groupPermissions[$uPermission]);
                }
                else
                {
                    $userSpecificPermissions[$uPermission] = Permission::PERMISSION_DENY;
                }
            }

            // if the value is allow or deny, we will check if this permission also existed on his group permissions
            // if it does, we will just remove it from there, we don't need it as it exist on users permissions
            // and it is more prioritize that permissions on the group
            else
            {
                if( array_key_exists($uPermission, $groupPermissions) )
                {
                    unset($groupPermissions[$uPermission]);
                }
            }
        }

        return array_merge($userSpecificPermissions, $groupPermissions);
    }

    /**
     * get all the special permissions of this user
     *
     * @return array
     */
    public function getSpecialPermissions()
    {
        $permissions = array();

        foreach ($this->permissions as $sp)
        {
            $permissions[$sp['key']] = $sp['value'];
        }

        return $permissions;
    }

    /**
     * get all the permissions of this user, this is the combined permissions
     * across all groups that the user is belong
     *
     * @return array
     */
    public function getGroupPermissions()
    {
        $permissions = array();

        $groups = $this->groups();

        $groups->each(function($group) use (&$permissions)
        {
            foreach($group->permissions as $gp)
            {
                // if the current permission is already on the permissions array
                // we will check if the value of the next same permission is a deny
                // if so, we will overwrite the value of the duplicated one
                // because if two groups has the same permission but different values,
                // the deny value will be prioritize
                if( array_key_exists($gp['key'], $permissions) )
                {
                    if( $gp['value'] == Permission::PERMISSION_DENY )
                    {
                        $permissions[$gp['key']] = $gp['value'];
                    }
                }
                else
                {
                    $permissions[$gp['key']] = $gp['value'];
                }
            }
        });

        return $permissions;
    }

    /**
     * logs last login date of the user
     */
    public function logLastLogin()
    {
        $this->last_login = $this->freshTimestamp();
        $this->save();
    }

    /**
     * get validation rules
     *
     * @return array
     */
    public function getValidationRules()
    {
        return self::$rules;
    }

    public function scopeOfName($query, $name)
    {
        if( $name === null || $name === '' ) return false;

        return $query->where('name','LIKE',"%{$name}%");
    }
    public function scopeOfEmail($query, $email)
    {
        if( $email === null || $email === '' ) return false;

        return $query->where('email','=',$email);
    }
    public function scopeOfGroups($q,$v)
    {
        if($v === false || $v === '' || count($v)==0 || $v[0]=='') return $q;

        return $q->whereHas('groups',function($q) use ($v)
        {
            return $q->whereIn('groups.id',$v);
        });
    }
}