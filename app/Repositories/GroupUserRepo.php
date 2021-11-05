<?php

namespace App\Repositories;

use App\Models\GroupUser;

use App\Repositories\Contracts\IGroupUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class GroupUserRepo extends Repository implements IGroupUser
{
    public function getAllGroupUsers($itemsPerPage = 0)
    {
        if($itemsPerPage > 0){
            return GroupUser::with(['group', 'user'])->OrderBy('firstname', 'asc', 'name', 'asc')->paginate($itemsPerPage);
        }
        return GroupUser::with(['group', 'user'])->OrderBy('firstname', 'asc', 'name', 'asc')->get();
    }

    public function getGroupUser($id)
    {
        return GroupUser::with(['group', 'user'])->find($id);
    }

    public function getUsersOfGroup($groupId)
    {
        return GroupUser::with(['group', 'user'])->where('group_id', $groupId)->OrderBy('firstname', 'asc', 'name', 'asc')->get();
    }

    public function getGroupsOfUser($userId)
    {
        return GroupUser::with(['group', 'user'])->where('user_id', $userId)->get();
    }

     /***************************************************************************
     Next function will create or update the user object in de database
     **************************************************************************/

     /**
     * set the data of a group user
     * @return Object
     */
    protected function setGroupUser(GroupUser $groupUser, array $data)
    {
        isset($data['firstname']) === true ? $groupUser->firstname = $data['firstname'] : "";
        isset($data['name']) === true ? $groupUser->name = $data['name'] : "";
        isset($data['group_id']) === true ? $groupUser->group_id = $data['group_id'] : "";
        isset($data['user_id']) === true ? $groupUser->user_id = $data['user_id'] : "";
        return $groupUser;
    }

    public function create(Array $data)
    {
        $groupUser = new GroupUser();
        $groupUser = $this->setGroupUser($groupUser, $data);
        $groupUser->code = $this->createCode();
        $groupUser->save();
        return $groupUser;
    }

    public function update(Array $data, $id)
    {
        $groupUser = $this->getGroupUser($id);
        $groupUser = $this->setGroupUser($groupUser, $data);
        $groupUser->save();
        return $groupUser;
    }

    //a group user is delete from the group but not removed from the database
    public function delete($id)
    {
        $groupUser = $this->getGroupUser($id);
        $groupUser->group_id = Null;
        return $groupUser->save();
        //return "User is removed from group";
        //return $groupUser->delete();
    }

    /**
     * create a unique code for a group user to join a group
     */
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

    public function regenerateGroupUserCode($id){
        $groupUser = $this->getGroupUser($id);
        if($groupUser->user_id != null){
            return false;
        }
        $groupUser->code = $this->createCode();
        $groupUser->save();
        return $groupUser;
    }
}