<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

use App\Services\UserService;
use Auth;

class ProfileController extends BaseController
{
    protected $userService;
    
    public function __construct(UserService $userService) 
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $userId = auth()->user()->id;
        return response()->json(new UserResource(User::find($userId)), 200);
    }
    
    public function update(ProfileRequest $request)
    {
        $userId = auth()->user()->id;
        $user = User::find($userId);
        $user->update($request->validated());
        $user->save();
        return response()->json(new UserResource($user), 201);
    }

    public function updatePassword(PasswordRequest $request){
        $userId = auth()->user()->id;
        $user = User::find($userId);
        $user->password = bcrypt($request['password']);
        $user->save();
        return $this->sendResponse($user, 'Password is changed');
    }

    public function destroy()
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $user = $this->userService->randomizeUser($user);
        $user->save();
        return response()->json("Profile is deleted", 204);
    }
}
