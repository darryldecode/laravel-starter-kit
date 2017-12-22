<?php

namespace App\Http\Controllers\Admin;

use App\Components\Core\Result;
use App\Components\User\Contracts\GroupRepository;
use App\Components\User\Models\Group;
use Illuminate\Http\Request;

class GroupController extends AdminController
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * GroupController constructor.
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = $this->groupRepository->index(request()->all());

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
            'name' => 'required',
            'permissions' => 'array',
        ]);

        if($validate->fails())
        {
            return $this->sendResponse(
                $validate->errors()->first(),
                null,
                400
            );
        }

        $results = $this->groupRepository->create($request->all());

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
        $results = $this->groupRepository->get($id);

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
            'name' => 'required',
            'permissions' => 'array',
        ]);

        if($validate->fails())
        {
            return $this->sendResponse(
                $validate->errors()->first(),
                null,
                400
            );
        }

        $results = $this->groupRepository->update($id,$request->all());

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
        // prevent delete of super user
        if($id == Group::SUPER_USER_GROUP_ID)
        {
            return $this->sendResponse(
                Result::MESSAGE_FORBIDDEN,
                null,
                403
            );
        }

        $results = $this->groupRepository->delete($id);

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData(),
            $results->getStatusCode()
        );
    }
}
