<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/11/2017
 * Time: 10:52 PM
 */

namespace App\Http\Controllers\Front;


use App\Contracts\FileRepository;
use Illuminate\Http\Request;

class FileController extends FrontController
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * FileController constructor.
     * @param FileRepository $fileRepository
     */
    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function filePreview(Request $request,$id)
    {
        $res = $this->fileRepository->previewFile([
            'id' => $id,
            'w' => $request->get('w',null),
            'h' => $request->get('h',null),
            'aspect_ratio' => $request->get('aspect_ratio',true),
            'up_size' => $request->get('up_size',true),
            'action' => $request->get('action','resize'), // resize|fit
        ]);

        return $res->getData();
    }
}