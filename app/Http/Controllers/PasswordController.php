<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Validators\UserValidation;
use App\Repositories\Contracts\IUser;

use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    /** @var App\Validators\UserValidation */
    protected $userValidation;
    /** @var App\Repositories\Contracts\IUser */
    protected $user;
    
    public function __construct(UserValidation $userValidation, Iuser $user) {
        //User must be logged in
        $this->middleware('auth');
        //User must have the correct rights to update
        $this->middleware('admin:Admin', ['except' => ['updateProfilePassword']]);
        
        $this->userValidation = $userValidation;
        $this->user = $user;
    }
   
    public function updateProfilePassword(Request $request)
    {
        $this->userValidation->validatProfilePassword($request);
        $this->user->updatePassword($request, Auth::user()->id);
        return redirect()->to('season/next-game/')->send();
    }

    public function updatePassword(Request $request, $id)
    {
        $this->userValidation->validatUserPassword($request);
        $this->user->updatePassword($request, $id);
        return redirect()->to('season/next-game/')->send();
    }
}
