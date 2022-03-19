<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Http\Requests\PasswordRequest;
//use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

use App\Repositories\Contracts\IUser;
use Auth;

class ProfileController extends BaseController
{
    /** @var App\Repositories\Contracts\IUser */
    protected $user;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Iuser $user) 
    {
        //$this->middleware('auth:api');
        $this->user = $user;
    }

    public function index()
    {
        return response()->json(new UserResource($this->user->getUser(auth()->user()->id)), 200);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileRequest $request)
    {
        $userId = auth()->user()->id;
        $user = $this->user->update($request->all(), $userId);
        return response()->json(new UserResource($user), 201);
    }

    public function updatePassword(PasswordRequest $request){
        $userId = auth()->user()->id;
        $user = $this->user->updatePassword($request->all(), $userId);
        $user = new UserResource($user);
        return $this->sendResponse($user, 'Password is changed');
    }

    public function destroy()
    {
        $userId = Auth::user()->id;
        $this->user->forgetUser($userId);
        return response()->json("Profile is deleted", 204);
    }
}
