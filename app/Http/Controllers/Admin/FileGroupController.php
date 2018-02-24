<?php

namespace App\Http\Controllers\Admin;

use App\Components\File\Contracts\IFileGroupRepository;
use Illuminate\Http\Request;

class FileGroupController extends AdminController
{
    /**
     * @var IFileGroupRepository
     */
    private $fileGroupRepository;

    /**
     * FileGroupController constructor.
     * @param IFileGroupRepository $fileGroupRepository
     */
    public function __construct(IFileGroupRepository $fileGroupRepository)
    {
        $this->fileGroupRepository = $fileGroupRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $results = $this->fileGroupRepository->index($request->all());

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData(),
            $results->getStatusCode()
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
        $validate = validator($request->all(),[
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if($validate->fails())
        {
            return $this->sendResponse(
                $validate->errors()->first(),
                null,
                400
            );
        }

        $results = $this->fileGroupRepository->create($request->all());

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData(),
            $results->getStatusCode()
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
        $results = $this->fileGroupRepository->get($id);

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData(),
            $results->getStatusCode()
        );
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
        $validate = validator($request->all(),[
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if($validate->fails())
        {
            return $this->sendResponse(
                $validate->errors()->first(),
                null,
                400
            );
        }

        $results = $this->fileGroupRepository->update($id,$request->all());

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData(),
            $results->getStatusCode()
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $results = $this->fileGroupRepository->delete($id);

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData(),
            $results->getStatusCode()
        );
    }
}
