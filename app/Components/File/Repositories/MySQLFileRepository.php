<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/9/2017
 * Time: 10:02 PM
 */

namespace App\Components\File\Repositories;


use App\Components\Core\Result;
use App\Components\Core\Utilities\Helpers;
use App\Components\File\Contracts\IFileRepository;
use App\Components\File\Models\File;
use App\Components\File\Utilities\FileHelper;
use Storage;
use Image;

class MySQLFileRepository implements IFileRepository
{

    /**
     * list resource
     *
     * @param array $params
     * @return Result
     */
    public function index($params)
    {
        // we need to transform group ids to array: 1,2,3,4 => [1,2,3,4]
        $groupIds = explode(',',Helpers::hasValue($params['file_group_id'],''));
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $name = Helpers::hasValue($params['name'],null);
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);

        $q = File::with(['user','group'])->orderBy($orderBy,$orderSort);

        if($name) $q = $q->where('name','like',"%{$name}%");
        if(count($groupIds) > 0 && !empty($groupIds[0])) $q = $q->whereIn('file_group_id',$groupIds);

        if($paginate==='yes')
        {
            return new Result(true,Result::MESSAGE_SUCCESS_LIST,$q->paginate($perPage));
        }

        return new Result(true,Result::MESSAGE_SUCCESS_LIST,$q->get());
    }

    /**
     * create resource
     *
     * @param array $data
     * @return Result
     */
    public function create($data)
    {
        $File = File::create([
            'name' => preg_replace('/[^A-Za-z0-9\-]/', ' ', $data['name']),
            'uploaded_by' => $data['uploaded_by'],
            'file_group_id' => $data['file_group_id'],
            'file_type' => $data['file_type'],
            'extension' => $data['extension'],
            'size' => $data['size'],
            'path' => $data['path'],
        ]);

        if(!$File) return new Result(false,Result::MESSAGE_FAILED_CREATE,null,400);

        return new Result(true,Result::MESSAGE_SUCCESS_CREATE,$File,201);
    }

    /**
     * get resource by id
     *
     * @param $id
     * @return Result
     */
    public function get($id)
    {
        $File = File::find($id);

        if(!$File) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        return new Result(true,Result::MESSAGE_SUCCESS,$File,200);
    }

    /**
     * update resource
     *
     * @param int $id
     * @param array $data
     * @return Result
     */
    public function update($id, $data)
    {
        $File = File::find($id);

        if(!$File) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        $File->uploaded_by = $data['uploaded_by'];
        $File->file_group_id = $data['file_group_id'];
        $File->file_type = $data['file_type'];
        $File->extension = $data['extension'];
        $File->size = $data['size'];
        $File->path = $data['path'];

        if(!$File->save()) return new Result(false,Result::MESSAGE_FAILED_UPDATE,null,400);

        return new Result(true,Result::MESSAGE_SUCCESS_UPDATE,$File,201);
    }

    /**
     * delete resource by id
     *
     * @param int $id
     * @return Result
     */
    public function delete($id)
    {
        $ids = explode(',',$id);

        foreach ($ids as $id)
        {
            $File = File::find($id);

            if(!$File) return new Result(false,"Failed to delete resource with id: {$id}. Error: ".Result::MESSAGE_NOT_FOUND,null,404);

            // delete file record
            $File->delete();

            // delete actual file
            $this->deleteFile($File->path);
        }

        return new Result(true,Result::MESSAGE_SUCCESS,$File,200);
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @return Result
     */
    public function upload($file)
    {
        $fileOriginalName = $file->getClientOriginalName();
        $filePath = $file->store('local');

        if(!$filePath) return new Result(false,"Failed to upload.",null,400);

        // other data
        $ext   = pathinfo(config('filesystems.disks.local.root').'/'.$filePath, PATHINFO_EXTENSION);
        $size  = Storage::disk('local')->getSize($filePath);
        $type  = Storage::disk('local')->getMimetype($filePath);

        $response = [
            'original_name' => $fileOriginalName,
            'path' => $filePath,
            'ext' => $ext,
            'size' => $size,
            'type' => $type,
        ];

        return new Result(true,'Upload success.',$response,200);
    }

    /**
     * @param string $path
     * @return Result
     */
    public function deleteFile($path)
    {
        if(Storage::disk('local')->exists($path))
        {
            Storage::disk('local')->delete($path);
        }

        return new Result(true,"file deleted",null);
    }

    /**
     * @param array $data
     * @return Result
     */
    public function previewFile($data)
    {
        $File = File::find($data['id']);
        $w = $data['w'];
        $h = $data['h'];
        $aspectRatio = $data['aspect_ratio'];
        $upSize = $data['up_size'];
        $action = $data['action'];

        // if file is not found, we will return a not found image
        if(!$File)
        {
            $img = Image::make(Storage::disk('public')->path(FileHelper::getIconPath('not-found')))->resize($w, $h, function($constraint) use ($aspectRatio,$upSize)
            {
                if($aspectRatio) $constraint->aspectRatio();
                if($upSize) $constraint->upsize();
            });
            $img = $img->response();

            return new Result(false,Result::MESSAGE_NOT_FOUND,$img,200);
        };

        // get the full path of the image record found
        $fullPath = Storage::disk('local')->path($File->path);

        // if image and not PSD, we will create an image instance and resize accordingly
        if(FileHelper::isImage($fullPath) && !FileHelper::isPSD($File->file_type))
        {
            if($action=='resize')
            {
                $img = Image::make($fullPath)->resize($w, $h, function($constraint) use ($aspectRatio,$upSize)
                {
                    if($aspectRatio) $constraint->aspectRatio();
                    if($upSize) $constraint->upsize();
                });
            }
            else
            {
                $img = Image::make($fullPath)->fit($w, $h, function($constraint) use ($aspectRatio,$upSize)
                {
                    if($upSize) $constraint->upsize();
                });
            }

            $res = $img->response();
        }

        // if its not an image, we will get the proper icon
        else
        {
            $fullPath = asset(FileHelper::getIconPath($File->file_type));
            $img = Image::make($fullPath)->resize(50, null, function($constraint) use ($aspectRatio,$upSize)
            {
                if($aspectRatio) $constraint->aspectRatio();
                if($upSize) $constraint->upsize();
            });
            $res = $img->response();
        }

        return new Result(false,Result::MESSAGE_NOT_FOUND,$res,200);
    }

    /**
     * @param int $id
     * @param string $token
     * @return Result
     */
    public function downloadFile($id, $token)
    {
        $File = File::find($id);

        if(!$File) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        if(!$File->verifyFileToken($token))
        {
            return new Result(false,"Invalid Token",null,403);
        }

        $fileFullPath = Storage::disk('local')->path($File->path);

        if(!Storage::disk('local')->exists($File->path))
        {
            return new Result(false,"File not exist.",null,404);
        }

        $fileDownload = response()->download($fileFullPath,str_replace(' ','_',$File->name).".{$File->extension}");

        return new Result(true,"Download success",$fileDownload,200);
    }
}