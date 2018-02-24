<?php

namespace App\Http\Controllers\Admin;

use App\Components\File\Contracts\IFileRepository;
use Illuminate\Http\Request;
use Auth;

class FileController extends AdminController
{
    /**
     * @var IFileRepository
     */
    private $fileRepository;

    /**
     * FileController constructor.
     * @param IFileRepository $fileRepository
     */
    public function __construct(IFileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $results = $this->fileRepository->index($request->all());

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data  = $request->all();
        $files = $request->file('file');
        $error = false;
        $errorMessage = '';

        foreach ($files as $file)
        {
            $res = $this->fileRepository->upload($file);

            if(!$res->isSuccessful())
            {
                $error = true;
                $errorMessage = $res->getMessage();
                break;
            }

            $fileData = $res->getData();
            $fileRecord = $this->fileRepository->create([
                'name' => $fileData['original_name'],
                'uploaded_by' => Auth::user()->id,
                'file_group_id' => $data['file_group_id'],
                'file_type' => $fileData['type'],
                'extension' => $fileData['ext'],
                'size' => $fileData['size'],
                'path' => $fileData['path'],
            ]);

            if(!$fileRecord->isSuccessful())
            {
                $this->fileRepository->deleteFile($res['path']);
                $error = true;
                $errorMessage = $fileRecord->getMessage();
                break;
            }
        }

        if($error)
        {
            return $this->sendResponse(
                $errorMessage,
                null,
                400
            );
        }

        return $this->sendResponse(
            $res->getMessage(),
            $res->getData()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $results = $this->fileRepository->delete($id);

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData()
        );
    }
}
