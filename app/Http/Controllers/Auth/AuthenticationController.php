<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;

use Socialite;
use Auth;
use App\Repositories\Contracts\IUser;

class AuthenticationController extends Controller
{
   /** @var App\Repositories\Contracts\IUser */
   protected $user;

   public function __construct(IUser $user){
       $this->user = $user;
   }

   /**
    * Redirect to the social providers login page
    */
    public function getSocialRedirect( $account ){
        try{
            return Socialite::with( $account )->redirect();
        }catch ( \InvalidArgumentException $e ){
            return redirect('/');
        }
    }

    /**
     * Handles the authentication
     */
    public function getSocialCallback( $account ){
        try {
            //get account info from the user who logged in
            $socialUser = Socialite::with( $account )->user();
            //check if the user exist
            $user = $this->user->existingUser($socialUser);
            //if user doesn't exist create one
            if($user == null){
                $user = $this->user->createSocialUser($socialUser);
            }
            //if the user exist or is created login
            Auth::login( $user );
            return redirect('/');
        }
        catch (Exception $e) {
            return 'error';
        }
    }

    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}
