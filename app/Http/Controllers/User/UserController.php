<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('user.index')->with('users', $this->user->getAllUsers());
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->userValidation->validateCreateUser($request);
        $this->user->create($request);
        return redirect()->to('user/')->send();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        //
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        return view('user.edit')->with('user', $this->user->getUser($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $this->userValidation->validateCreateUser($request, $id);
        $this->user->update($request, $id);
        return redirect()->to('user/')->send();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $this->user->delete($id);
        return redirect()->to('user/')->send();
    }
}
