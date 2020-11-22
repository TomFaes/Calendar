<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;

use Socialite;
//use Auth;
use App\Repositories\Contracts\IUser;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    /** @var App\Repositories\Contracts\IUser */
    protected $user;

   public function __construct(IUser $user)
   {
        $this->user = $user;
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
            $user = $this->user->checkEmail($socialUser->getEmail());
            //if user doesn't exist create one
            if ($user == null) {
                $user = $this->user->createSocialUser($socialUser);
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

    public function login()
    { 
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')]))
        { 
            $user = $this->user->checkEmail(request('email'));
            Auth::login($user, true);            
            return redirect('/');
        } else { 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
