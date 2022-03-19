<?php

namespace App\Http\Controllers\Auth;

/*
use App\Http\Controllers\Controller;

use Socialite;
//use Auth;
use App\Repositories\Contracts\IUser;
use Illuminate\Support\Facades\Auth;
*/

//use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;


use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Validator;
use Socialite;

use App\Repositories\Contracts\IUser;

class AuthenticationController extends BaseController
{
    /** @var App\Repositories\Contracts\IUser */
    protected $userRepo;

   public function __construct(IUser $userRepo)
   {
        $this->userRepo = $userRepo;
   }

   /**
    * Redirect to the social providers login page
    */

    public function getSocialRedirect($account)
    {
        try{
            return Socialite::with($account)->redirect();
        }catch ( \InvalidArgumentException $e ){
            return redirect('/');
        }
    }

    /**
     * Handles the authentication
     */

    public function getSocialCallback( $account )
    {
        try {
            //get account info from the user who logged in
            $socialUser = Socialite::with($account)->user();
            //check if the user exist
            $user = $this->userRepo->checkEmail($socialUser->getEmail());
            //if user doesn't exist create one
            if ($user == null) {
                $user = $this->userRepo->createSocialUser($socialUser);
            }
            //if the user exist or is created login
            Auth::login($user, true);
            Auth::user()->createToken('tenniskalender')->accessToken;
            return redirect('/');
        }
        catch (Exception $e) {
            return 'error';
        }
    }


    public function login(LoginRequest $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $authUser = Auth::user();
            $success['token'] =  $authUser->createToken('MyAuthApp')->plainTextToken;
            $success['firstname'] =  $authUser->first_name;
            $success['name'] =  $authUser->first_name;
            $success['full_name'] =  $authUser->first_name." ".$authUser->name;

            return $this->sendResponse($success, 'User signed in');
        }
        else{
            return $this->sendError("Email and password doesn't match a known user", ['error'=>'Unauthorised']);
        }
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        Auth::guard('web')->logout();
        return $this->sendResponse('You are logged out', 'You have successfully logged out and the token was successfully deleted');
    }

/*
    public function login()
    { 
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')]))
        { 
            $user = $this->userRepo->checkEmail(request('email'));
            Auth::login($user, true);            
            return redirect('/');
        } else { 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
*/
/*
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
*/
}
