<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/11/2017
 * Time: 10:52 PM
 */

namespace App\Http\Controllers\Front;


use App\Components\File\Contracts\IFileRepository;
use App\Components\File\Repositories\FileRepository;
use App\Components\File\Services\FileService;
use Illuminate\Http\Request;

class FileController extends FrontController
{
    /**
     * @var FileRepository
     */
    private $fileRepository;
    /**
     * @var FileService
     */
    private $fileService;

    /**
     * FileController constructor.
     * @param FileRepository $fileRepository
     * @param FileService $fileService
     */
    public function __construct(FileRepository $fileRepository, FileService $fileService)
    {
        $this->fileRepository = $fileRepository;
        $this->fileService = $fileService;
    }

    /**
     * preview file web request
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function filePreview(Request $request,$id)
    {
        $file = $this->fileService->previewFile([
            'id' => $id,
            'w' => $request->get('w',null),
            'h' => $request->get('h',null),
            'aspect_ratio' => $request->get('aspect_ratio',true),
            'up_size' => $request->get('up_size',true),
            'action' => $request->get('action','resize'), // resize|fit
        ]);

        return $file;
    }

    /**
     * download file web request
     *
     * @param Request $request
     * @param int $id
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|null|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function fileDownload(Request $request, $id)
    {
        $token = $request->get('file_token');

        $res = $this->fileService->downloadFile($id,$token);

        if($res) return $res;

        return view('errors.403');
    }
}