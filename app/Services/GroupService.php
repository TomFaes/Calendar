<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\IGroupUser;
use App\Repositories\Contracts\IUser;

class GroupService
{
    protected $user;
    protected $groupUser;

    public function __construct(IUser $user, IGroupUser $groupUser)
    {
        $this->user = $user;
        $this->groupUser = $groupUser;
    }

    /**
     * this function will check if the new user is already added to some existing groups
     */
    public function checkNewUser(User $user){
        if(isset($user->id) === true){
            $this->addUserToGroupUser($user);
            return true;
        }
        return false;
    }

    /**
     * check if a users email is used in the group users
     */
    public function checkExistingUser($email)
    {
        $user = $this->user->checkEmail($email);

        if (isset($user->id) === true) {
            $this->addUserToGroupUser($user);
            return true;
        }
        return false;
    }

    /**
     * this method will add the user id to the groupuser
     */
    protected function addUserToGroupUser(User $user)
    {
        //get all groups where there is a user with the given email adres and doesn't have a user id
        $groupsOfUser = $this->groupUser->getGroupsBasedOnEmail($user->email);
        $data['user_id'] = $user->id;
        //add the user to all groups
        foreach ($groupsOfUser AS $groupUser) {
            $this->groupUser->update($data, $groupUser->id);
        }
    }
}