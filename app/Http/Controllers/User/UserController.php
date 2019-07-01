<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Validators\UserValidation;
use App\Repositories\Contracts\IUser;


class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | User Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling of Users
    |
    */
    /** @var App\Validators\UserValidation */
    protected $userValidation;
    /** @var App\Repositories\Contracts\IUser */
    protected $user;
    
    public function __construct(UserValidation $userValidation, Iuser $user) {
        $this->middleware('auth');
        $this->userValidation = $userValidation;
        $this->user = $user;
    }
    
    public function index(){
        return view('user.index')->with('users', $this->user->getAllUsers());
    }
    
    public function create(){
        return view('user.create');
    }

    
    public function store(Request $request){
        $this->userValidation->validateCreateUser($request);
        $this->user->create($request);
        return redirect()->to('user/')->send();
    }

    
    public function show($id){
        //
    }

    
    public function edit($id){
        return view('user.edit')->with('user', $this->user->getUser($id));
    }

    
    public function update(Request $request, $id){
        $this->userValidation->validateCreateUser($request, $id);
        $this->user->update($request, $id);
        return redirect()->to('user/')->send();
    }

    
    public function destroy($id){
        $this->user->delete($id);
        return redirect()->to('user/')->send();
    }
    
    public function updatingProfile(){
        return view('user.edit')->with('user', $this->user->getUser(Auth::user()->id));
    }
    
    public function updateProfile(Request $request){
        $this->userValidation->validateCreateUser($request,  Auth::user()->id);
        $this->user->update($request, Auth::user()->id);

        //add a redirect to the home page
        return redirect()->to('season/next-game/')->send();
    }    
}
