<?php

namespace App\Services;


use App\Models\GroupUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GroupUserService 
{
    public function createCode()
    {
        $createCodeString = "";
        for($x=0; $x < 5; $x++){
            $createCodeString = microtime().Str::random(20);
            $createCodeString = Hash::make($createCodeString);

            $checkIfCodeExist = GroupUser::where('code', $createCodeString)->first();
            if($checkIfCodeExist == null){
                return $createCodeString;
            }
        }
        return false;
    }

    public function joinGroup($code, $userId)
    {
        if(strlen($code) <= 0){
            return false;
        }
        $groupUser = GroupUser::where('code', $code)->first();

        if($groupUser == null){
            return false;
        }
        $groupUser->code = Null;
        $groupUser->user_id = $userId;
        $groupUser->save();
        return $groupUser;
    }

    public function regenerateGroupUserCode(GroupUser $groupUser){
        if($groupUser->user_id != null){
            return false;
        }
        $groupUser->code = $this->createCode();
        $groupUser->save();
        return $groupUser;
    }

    


}
