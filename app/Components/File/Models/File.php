<?php

namespace App\Components\File\Models;

use App\Components\User\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 * @package App\Components\File\Models
 *
 * @property int $id
 * @property string $name
 * @property int $uploaded_by
 * @property int $file_group_id
 * @property string $file_type
 * @property string $extension
 * @property int $size
 * @property string $path
 * @property string $file_token
 */
class File extends Model
{
    const TAG = 'WASK_FILE_MANAGER';

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

    protected $appends = ['file_token'];

    /**
     * generate a token on each file on the fly that will give authorization to be downloaded
     *
     * @return string
     */
    public function getFileTokenAttribute()
    {
        $key = env('FILE_DOWNLOAD_SECRET');
        $token = array(
            "iss" => env('APP_URL'),
            "aud" => env('APP_URL'),
            "iat" => (Carbon::now())->timestamp,
            "exp" => (Carbon::now()->addDays(1))->timestamp,
            "file_id" => $this->id,
        );
        return JWT::encode($token, $key);
    }

    /**
     * verify the token
     *
     * @param $token
     * @return bool|object
     */
    public function verifyFileToken($token)
    {
        try {
            $key = env('FILE_DOWNLOAD_SECRET');
            $decoded = JWT::decode($token, $key, array('HS256'));
        } catch (\Exception $e)
        {
            \Log::error(self::TAG . ": Invalid file token.");
            return false;
        }

        if(!$decoded->file_id==$this->id)
        {
            return false;
        }

        return $decoded;
    }

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
