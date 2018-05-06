<?php

namespace App\Http\Controllers\Admin;

use App\Components\Core\Result;
use App\Components\User\Models\Permission;
use App\Components\User\Repositories\PermissionRepository;
use Illuminate\Http\Request;

class PermissionController extends AdminController
{
    /**
     * @var PermissionRepository
     */
    private $permissionRepository;

    /**
     * PermissionController constructor.
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->permissionRepository->index(request()->all());

        return $this->sendResponseOk($data,"get permissions ok.");
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
            'title' => 'required|string',
            'description' => 'required|string',
            'key' => 'required|string|unique:permissions',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $permission = $this->permissionRepository->create($request->all());

        if(!$permission) return $this->sendResponseBadRequest("Failed to create");

        return $this->sendResponseCreated($permission);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = $this->permissionRepository->find($id);

        if(!$permission) return $this->sendResponseNotFound();

        return $this->sendResponseOk($permission);
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
            'title' => 'required|string',
            'description' => 'required|string',
            'key' => 'required|string',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $updated = $this->permissionRepository->update($id,$request->all());

        if(!$updated) return $this->sendResponseBadRequest("Failed update.");

        return $this->sendResponseUpdated();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepository->find($id);

        if(!$permission) return $this->sendResponseNotFound();

        // prevent delete of super user permission
        if($permission->key == Permission::SUPER_USER_PERMISSION_KEY)
        {
            return $this->sendResponseBadRequest("Cannot delete permission.");
        }

        $this->permissionRepository->delete($id);

        return $this->sendResponseDeleted();
    }
}
