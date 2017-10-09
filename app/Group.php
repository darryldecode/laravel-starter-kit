<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    const SUPER_USER_GROUP_ID = 1;
    const DEFAULT_USER_GROUP_ID = 2;

    const PERMISSION_ALLOW 	= 1;
    const PERMISSION_DENY 	= -1;

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
        return $this->users->count();
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
}
