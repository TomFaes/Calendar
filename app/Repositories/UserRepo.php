<?php

namespace App\Repositories;

use Illuminate\Http\Request;

use App\Models\User;

class UserRepo extends Repository implements Contracts\IUser {
    public function getAllUsers(){
        return User::all();
    }
    
    public function getUser($id){
        return User::find($id);
    }

    public function getUsersFromList($listUsers){
        return User::whereIn('id', $listUsers)->get();
    }
    
    /***************************************************************************
     Next function will create or update the user object in de database
     **************************************************************************/
    
    protected function setUser(User $user, $request){
        $request->input('firstname') != "" ? $user->firstname = $request->input('firstname') : "";
        $request->input('name') != "" ? $user->name = $request->input('name') : "";
        $request->input('email') != "" ? $user->email = $request->input('email') : "";
        if($request->input('role') != ""){
            $user->role = $request->input('role');
        }
        //password can be set if it has been changed
        if($request->input('password') != ""){
            $user->password = bcrypt($request->input('password'));
        }
        return $user;
    }
    
    public function create(Request $request){
        $user = new User();
        //default role is User
        $user->role = 'User';
        $user = $this->setUser($user, $request);
        $user->save();
        return $user;
    }
    
    public function update(Request $request, $userId){
        $user = $this->getUser($userId);
        $user = $this->setUser($user, $request);
        $user->save();
        return $user;
    }

    public function updatePassword(Request $request, $userId){
        $user = $this->getUser($userId);
        if($request->input('password') != ""){
            echo "B";
            $user->password = bcrypt($request->input('password'));
        }
        $user->save();
        return $user;
    }

    public function delete($userId){
        $user = $this->getUser($userId);
        $user->delete();
    }
}
