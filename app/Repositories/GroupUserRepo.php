<?php

namespace App\Repositories;

use App\Models\GroupUser;

use App\Repositories\Contracts\IGroupUser;


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
        return GroupUser::with(['group', 'user'])->where('user_id', $userId)->where('verified',1)->OrderBy('firstname', 'asc', 'name', 'asc')->get();
    }

    public function getUnverifiedGroupUsers($userId)
    {
        return GroupUser::with(['group', 'user'])->where('user_id', $userId)->where('verified',0)->OrderBy('firstname', 'asc', 'name', 'asc')->get();
    }

    public function getGroupsBasedOnEmail($email)
    {
        return GroupUser::with(['group', 'user'])->where('email', $email)->where('verified',0)->where('user_id', null)->OrderBy('name', 'asc', 'firstname', 'asc')->get();
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
        isset($data['email']) === true ? $groupUser->email = $data['email'] : "";
        isset($data['group_id']) === true ? $groupUser->group_id = $data['group_id'] : "";
        isset($data['user_id']) === true ? $groupUser->user_id = $data['user_id'] : "";
        return $groupUser;
    }

    public function create(Array $data)
    {
        $groupUser = new GroupUser();
        $groupUser = $this->setGroupUser($groupUser, $data);
        $groupUser->verified = 0;
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

    public function delete($id)
    {
        $groupUser = $this->getGroupUser($id);
        return $groupUser->delete();
    }

    public function verifyUser($id)
    {
        $groupUser = $this->getGroupUser($id);
        $groupUser->verified = 1;
        $groupUser->save();
        return $groupUser;
    }

    public function unverifyUser($id)
    {
        $groupUser = $this->getGroupUser($id);
        $groupUser->user_id = null;
        $groupUser->email = null;
        $groupUser->verified = 0;
        $groupUser->save();
        return $groupUser;
    }
}