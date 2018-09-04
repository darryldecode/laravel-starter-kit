<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 6:15 AM
 */

namespace App\Http\Controllers\Admin;

use App\Components\Core\Utilities\Helpers;
use App\Components\User\Models\User;
use App\Components\User\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends AdminController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->userRepository->listUsers(request()->all());

        return $this->sendResponseOk($data,"list users ok.");
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'permissions' => 'array',
            'groups' => 'array',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        /** @var User $user */
        $user = $this->userRepository->create($request->all());

        if(!$user) return $this->sendResponseBadRequest("Failed create.");

        // attach to group
        if($groups = $request->get('groups',[]))
        {
            foreach ($groups as $groupId => $shouldAttach)
            {
                if($shouldAttach) $user->groups()->attach($groupId);
            }
        }

        return $this->sendResponseCreated($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->userRepository->find($id,['groups']);

        if(!$user) return $this->sendResponseNotFound();

        return $this->sendResponseOk($user);
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
            'email' => 'required|email',
            'permissions' => 'array',
            'groups' => 'array',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $payload = $request->all();

        // if password field is present but has empty value or null value
        // we will remove it to avoid updating password with unexpected value
        if(!Helpers::hasValue($payload['password'])) unset($payload['password']);

        $updated = $this->userRepository->update($id,$payload);

        if(!$updated) return $this->sendResponseBadRequest("Failed update");

        // re-sync groups

        /** @var User $user */
        $user = $this->userRepository->find($id);

        $groupIds = [];

        if($groups = $request->get('groups',[]))
        {
            foreach ($groups as $groupId => $shouldAttach)
            {
                if($shouldAttach) $groupIds[] = $groupId;
            }
        }

        $user->groups()->sync($groupIds);

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
        // do not delete self
        if($id == auth()->user()->id) return $this->sendResponseForbidden();

        try {
            $this->userRepository->delete($id);
        } catch (\Exception $e) {
            return $this->sendResponseBadRequest("Failed to delete");
        }

        return $this->sendResponseDeleted();
    }
}