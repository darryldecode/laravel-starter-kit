<?php

namespace App\Http\Controllers\Admin;

use App\Components\File\Models\File;
use App\Components\File\Repositories\FileRepository;
use App\Components\File\Services\FileService;
use Illuminate\Http\Request;
use Auth;
use League\Flysystem\FileNotFoundException;

class FileController extends AdminController
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
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $results = $this->fileRepository->index($request->all());

        return $this->sendResponseOk($results);
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
        $fileRecord = null;

        foreach ($files as $file)
        {
            try {
                $fileData = $this->fileService->upload($file);
            } catch (FileNotFoundException $e) {
                $error = true;
                $errorMessage = $e->getMessage();
                break;
            }

            if(!$fileData)
            {
                $error = true;
                $errorMessage = $this->fileService->getErrors()->first();
                break;
            }

            /** @var File $fileRecord */
            $fileRecord = $this->fileRepository->create([
                'name' => $fileData['original_name'],
                'uploaded_by' => Auth::user()->id,
                'file_group_id' => $data['file_group_id'],
                'file_type' => $fileData['type'],
                'extension' => $fileData['ext'],
                'size' => $fileData['size'],
                'path' => $fileData['path'],
            ]);

            if(!$fileRecord)
            {
                $this->fileService->deleteFile($fileData['path']);
                $error = true;
                $errorMessage = "Failed to create record.";
                break;
            }
        }

        if($error)
        {
            return $this->sendResponseBadRequest($errorMessage);
        }

        return $this->sendResponseCreated($fileRecord);
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
        try {
            $this->fileRepository->deleteRecordAndFile($id);
        } catch (\Exception $e) {
            return $this->sendResponseBadRequest("Failed to delete file.");
        }

        return $this->sendResponseDeleted();
    }
}
