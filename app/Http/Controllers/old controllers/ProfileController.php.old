<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validators\UserValidation;
use App\Repositories\Contracts\IUser;
use Illuminate\Support\Facades\Auth;

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
    public function __construct(UserValidation $userValidation, Iuser $user) {
        $this->middleware('auth');
        $this->userValidation = $userValidation;
        $this->user = $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(){
        return view('user.edit')->with('user', $this->user->getUser(Auth::user()->id));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        $this->userValidation->validateCreateUser($request,  Auth::user()->id);
        $this->user->update($request, Auth::user()->id);
        //add a redirect to the home page
        return redirect()->to('/')->send();
        //return redirect()->to('season/next-game/')->send();
    }
}
