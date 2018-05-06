<?php

namespace App\Http\Controllers\Admin;

use App\Components\File\Repositories\FileGroupRepository;
use Illuminate\Http\Request;

class FileGroupController extends AdminController
{
    /**
     * @var FileGroupRepository
     */
    private $fileGroupRepository;

    /**
     * FileGroupController constructor.
     * @param FileGroupRepository $fileGroupRepository
     */
    public function __construct(FileGroupRepository $fileGroupRepository)
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
        $data = $this->fileGroupRepository->index($request->all());

        return $this->sendResponseOk($data);
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
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $file = $this->fileGroupRepository->create($request->all());

        if(!$file)  return $this->sendResponseBadRequest("Failed to create.");

        return $this->sendResponseCreated($file);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $file = $this->fileGroupRepository->find($id);

        if(!$file) return $this->sendResponseNotFound();

        return $this->sendResponseOk($file);
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

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $updated = $this->fileGroupRepository->update($id,$request->all());

        if(!$updated) return $this->sendResponseBadRequest("Failed to update");

        return $this->sendResponseOk([],"Updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->fileGroupRepository->delete($id);

        return $this->sendResponseOk([],"Deleted.");
    }
}
