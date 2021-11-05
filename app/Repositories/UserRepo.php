<?php

namespace App\Repositories;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepo extends Repository implements Contracts\IUser {
    
    public function getAllUsers($itemsPerPage = 0)
    {
        if ($itemsPerPage > 0) {
            return User::paginate($itemsPerPage);
        }
        return User::all();
    }
    
    public function getUser($id)
    {
        return User::find($id);
    }

    /**
     * needed when logged in with password and email
     */
    public function checkEmail($email)
    {
        return User::where('email', $email)->first();
    }
    
    /***************************************************************************
     Next function will create or update the user object in de database
     **************************************************************************/
    
    protected function setUser(User $user, array $data)
    {
        isset($data['firstname']) === true ? $user->firstname = $data['firstname'] : "";
        isset($data['name']) === true ? $user->name = $data['name'] : "";
        isset($data['email']) === true ? $user->email = $data['email'] : "";

        if (isset($data['role']) === true) {
            $user->role = $data['role'];
        }
        if (isset($data['password']) === true) {
            $user->password = bcrypt($data['password'] );
        }
        return $user;
    }
    
    public function create(Array $data)
    {
        $user = new User();
        //default role is User
        $user->role = 'User';
        $user = $this->setUser($user, $data);
        $user->password = Hash::make(Str::random(16));
        $user->save();
        return $user;
    }

    /**
     * Creates a user who has been logged in trough Socialite(google, facebook, ....)
     */
    public function createSocialUser($socialUser)
    {
        $newUser = new User();
        $newUser->firstname = $socialUser['given_name'];
        $newUser->name = $socialUser['family_name'];
        $newUser->email = $socialUser['email'];
        $newUser->password = Hash::make(Str::random(16));
        $newUser->role = 'User';
        $newUser->save();
        return $newUser;
    }
    
    
    public function update(Array $data, $userId)
    {
        $user = $this->getUser($userId);
        $user = $this->setUser($user, $data);
        $user->save();
        return $user;
    }

    public function updatePassword(Array $data, $userId)
    {
        $user = $this->getUser($userId);
        if (isset($data['password']) === true) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();
        return $user;
    }

    /**
     * instead of deleting a user, we will randomize the data
     */
    public function forgetUser($userId)
    {
        $user = $this->getUser($userId);
        $user->firstname = Str::random(10);
        $user->name = Str::random(10);
        $user->email = Str::random(10)."@".Str::random(10).".".Str::random(10);
        $user->password = Hash::make(Str::random(16));
        $user->forget_user = 1;
        $user->save();
        return $user;
    }

    /**
     * method is not used for now
     */
    public function delete($userId)
    {
        return $this->forgetUser($userId);
        //return response()->json("User is deleted", 204);
    }
}
