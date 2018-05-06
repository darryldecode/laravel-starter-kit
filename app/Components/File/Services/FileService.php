<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 5/6/2018
 * Time: 1:14 PM
 */

namespace App\Components\File\Services;


use App\Components\Core\BaseService;
use App\Components\File\Models\File;
use App\Components\File\Repositories\FileRepository;
use App\Components\File\Utilities\FileHelper;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileService extends BaseService
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * FileService constructor.
     * @param FileRepository $fileRepository
     */
    public function __construct(FileRepository $fileRepository)
    {
        parent::__construct();
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @return array|bool
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function upload($file)
    {
        $fileOriginalName = $file->getClientOriginalName();
        $filePath = $file->store('local');

        if(!$filePath)
        {
            $this->addError("Failed to upload.",400);
            return false;
        }

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

        return $response;
    }

    /**
     * @param string $path
     * @return void
     */
    public function deleteFile($path)
    {
        if(Storage::disk('local')->exists($path))
        {
            Storage::disk('local')->delete($path);
        }
    }

    /**
     * @param array $data
     * @return \Intervention\Image\Image|mixed
     */
    public function previewFile($data)
    {
        $File = $this->fileRepository->find($data['id']);
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

            return $img;
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

        return $res;
    }

    /**
     * @param int $id
     * @param string $token
     * @return bool|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadFile($id, $token)
    {
        /** @var File $File */
        $File = $this->fileRepository->find($id);

        if(!$File)
        {
            $this->addError("File not found.",404);
            return false;
        }

        if(!$File->verifyFileToken($token))
        {
            $this->addError("Invalid Token",403);
            return false;
        }

        $fileFullPath = Storage::disk('local')->path($File->path);

        if(!Storage::disk('local')->exists($File->path))
        {
            $this->addError("File not exist.",404);
            return false;
        }

        return response()->download($fileFullPath,str_replace(' ','_',$File->name).".{$File->extension}");
    }
}