<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordRequest;
use Illuminate\Http\Request;

use App\Repositories\Contracts\IUser;

use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    /** @var App\Repositories\Contracts\IUser */
    protected $user;
    
    public function __construct(Iuser $user) {
        //User must be logged in
        $this->middleware('auth');
        //User must have the correct rights to update
        $this->middleware('admin:Admin', ['except' => ['updateProfilePassword']]);
        
        $this->user = $user;
    }
   
    /**
     * Update the the authenticated user password in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfilePassword(PasswordRequest $request)
    {
        $this->user->updatePassword($request, Auth::user()->id);
        return redirect()->to('/')->send();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(PasswordRequest $request, $id)
    {
        $this->user->updatePassword($request, $id);
        return redirect()->to('/user')->send();
    }
}
