<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 6:15 AM
 */

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Contracts\UserRepository;

class UserController extends AjaxController
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
        $results = $this->userRepository->listUsers(request()->all());

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
        $validate = validator($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'permissions' => 'array',
            'groups' => 'array',
        ]);

        if($validate->fails())
        {
            return $this->sendResponse(
                $validate->errors()->first(),
                null,
                400
            );
        }

        $results = $this->userRepository->create($request->all());

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData()
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
        $results = $this->userRepository->get($id);

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData()
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
            'email' => 'required|email',
            'permissions' => 'array',
            'groups' => 'array',
        ]);

        if($validate->fails())
        {
            return $this->sendResponse(
                $validate->errors()->first(),
                null,
                400
            );
        }

        $results = $this->userRepository->update($id,$request->all());

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData()
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
        $results = $this->userRepository->delete($id);

        return $this->sendResponse(
            $results->getMessage(),
            $results->getData()
        );
    }
}