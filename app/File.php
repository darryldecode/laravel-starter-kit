<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'name',
        'uploaded_by',
        'file_group_id',
        'file_type',
        'extension',
        'size',
        'path',
    ];

    /**
     * the owner of the file
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class,'uploaded_by');
    }

    /**
     * the group the file belongs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(FileGroup::class,'file_group_id');
    }

    /**
     * get the storage path
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function getStoragePath()
    {
        return config('filesystems.local');
    }
}
