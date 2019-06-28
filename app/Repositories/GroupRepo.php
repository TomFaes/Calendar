<?php

namespace App\Repositories;

use Illuminate\Http\Request;

use App\Models\Group;

class GroupRepo extends Repository implements Contracts\IGroup {
    
    public function getAllGroups(){
        return Group::all();
    }
    
    public function getGroup($id){
        return Group::find($id);
    }
    
    public function getArrayOfGroupUsers($groupId){
        $groupUserArray = array();
        $group = Group::find($groupId);
        foreach($group->Users as $groupUser){
            $groupUserArray[$groupUser->id] = $groupUser->id;
        }
        return $groupUserArray;
    }
    
    public function getArrayOfUserGroups($userId){
        $arrayGroups = array();
        $groups = $this->getAllGroups();
        
        foreach($groups as $group){
            foreach($group->Users as $groupUser){
                if($groupUser->id == $userId){
                    $arrayGroups[$group->id] = $group->id;
                }
            }
        }
        return $arrayGroups;
    }
    
    /***************************************************************************
     Next function will create or update the group object in de database
     **************************************************************************/
    
    protected function setGroup(Group $group, $request){
        $request->input('name') != "" ? $group->name = $request->input('name') : "";
        $request->input('userId') != "" ? $group->admin_id = $request->input('userId') : "";
        return $group;
    }
    
    public function create(Request $request){
        $group = new Group();
        $group = $this->setGroup($group, $request);
        $group->save();
        return $group;
    }
    
    public function update(Request $request, $groupId){
        $group = $this->getGroup($groupId);
        $group = $this->setGroup($group, $request);
        $group->save();
        return $group;
    }
    
    public function addUsers(Request $request, $groupId){
        $group = $this->getGroup($groupId);
        foreach($request['groupUsers'] AS $userId){
            $group->users()->attach($userId);
        }
        return $group;
    }
    
    public function deleteGroupUser($userId, $groupId){
        $group = $this->getGroup($groupId);
        $group->users()->detach($userId);
        return $group;
    }

    public function delete($groupId){
        $group = $this->getGroup($groupId);
        $group->delete();
    }
}
