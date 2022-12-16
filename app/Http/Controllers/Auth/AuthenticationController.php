<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Socialite;

use App\Services\UserService;

class AuthenticationController extends BaseController
{
    protected $userService;

   public function __construct(UserService $userService)
   {
        $this->userService = $userService;
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
            $user = User::where('email', $socialUser->getEmail())->first();
            //if user doesn't exist create one
            if ($user == null) {
                $user = $this->userService->setupSocialUser($socialUser);
                $user->save();
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
}
