<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService 
{
    /**
     * randomize the user data.
     */
    public function randomizeUser(User $user)
    {
        $user->firstname = Str::random(10);
        $user->name = Str::random(10);
        $user->email = Str::random(10)."@".Str::random(10).".".Str::random(10);
        $user->password = Hash::make(Str::random(16));
        $user->forget_user = 1;
        return $user;
    }

    public function setupSocialUser($socialUser){
        $newUser = new User();
        $newUser->firstname = $socialUser['given_name'];
        $newUser->name = $socialUser['family_name'];
        $newUser->email = $socialUser['email'];
        $newUser->password = Hash::make(Str::random(16));
        $newUser->role = 'User';
        return $newUser;
    }
}
