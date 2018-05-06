<?php

namespace App\Components\File\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FileGroup
 * @package App\Components\File\Models
 *
 * @property int $id
 * @property string $name
 * @property string $description
 */
class FileGroup extends Model
{
    protected $table = 'file_groups';

    protected $fillable = [
        'name',
        'description'
    ];

    protected $appends = ['file_count'];

    /**
     * get the file count attribute
     *
     * @return mixed
     */
    public function getFileCountAttribute()
    {
        return $this->files()->count();
    }

    /**
     * the files on this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class,'file_group_id');
    }
}
