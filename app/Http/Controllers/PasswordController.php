<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{    
    public function __construct() {
        //User must be logged in
        $this->middleware('auth');
        //User must have the correct rights to update
        $this->middleware('admin:Admin', ['except' => ['updateProfilePassword']]);
    }
   
    /**
     * Update the the authenticated user password in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfilePassword(PasswordRequest $request)
    {
        $user = User::find(Auth::user()->id);
        $user->password = bcrypt($request['password']);
        $user->save();
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
        $user = User::find($id);
        $user->password = bcrypt($request['password']);
        $user->save();
        return redirect()->to('/user')->send();
    }
}
