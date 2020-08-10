<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validators\UserValidation;
use App\Repositories\Contracts\IUser;
use Auth;

class ProfileController extends Controller
{
    /** @var App\Validators\UserValidation */
    protected $userValidation;
    /** @var App\Repositories\Contracts\IUser */
    protected $user;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(UserValidation $userValidation, Iuser $user) 
    {
        $this->middleware('auth:api');
        $this->userValidation = $userValidation;
        $this->user = $user;
    }

    public function index()
    {
        return response()->json($this->user->getUser(auth()->user()->id), 200);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $userId = auth()->user()->id;
        $this->userValidation->validateCreateUser($request, $userId);
        $user = $this->user->update($request->all(), $userId);
        return response()->json($user, 201);
    }

    public function destroy()
    {
        $userId = Auth::user()->id;
        $this->user->forgetUser($userId);
        return response()->json("Profile is deleted", 204);
    }
}
