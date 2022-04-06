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
    protected $userRepo;
    
    public function __construct(Iuser $user) 
    {
        $this->userRepo = $user;
    }

    public function index()
    {
        return response()->json(new UserResource($this->userRepo->getUser(auth()->user()->id)), 200);
    }
    
    public function update(ProfileRequest $request)
    {
        $userId = auth()->user()->id;
        $user = $this->userRepo->update($request->all(), $userId);
        return response()->json(new UserResource($user), 201);
    }

    public function updatePassword(PasswordRequest $request){
        $userId = auth()->user()->id;
        $user = $this->userRepo->updatePassword($request->all(), $userId);
        $user = new UserResource($user);
        return $this->sendResponse($user, 'Password is changed');
    }

    public function destroy()
    {
        $userId = Auth::user()->id;
        $this->userRepo->forgetUser($userId);
        return response()->json("Profile is deleted", 204);
    }
}
