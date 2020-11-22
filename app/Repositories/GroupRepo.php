<?php

namespace App\Repositories;

use Illuminate\Http\Request;

use App\Models\Group;

class GroupRepo extends Repository implements Contracts\IGroup 
{
    public function getGroups($itemsPerPage = 0)
    {
        if ($itemsPerPage > 0) {
            return Group::with(['groupUsers', 'admin'])->OrderBy('name', 'asc')->paginate($itemsPerPage);
        }
        return Group::with(['groupUsers', 'admin'])->OrderBy('name', 'asc')->get();
    }
    
    public function getGroup($id)
    {
        return Group::find($id);
    }

    /**
     * Get all groups of a user where he is either admin or member
     * @return Object
     */
    public function getUserGroups($userId, $itemsPerPage = 0)
    { 
        
        if ($itemsPerPage > 0) {
            return Group::whereHas('groupUsers', function ($query) use ($userId) {
                $query->where('user_id', '=', $userId)->where('verified', 1);
            })->orWhere('admin_id', $userId)->with(['groupUsers','admin'])->paginate($itemsPerPage);
        }

        return Group::whereHas('groupUsers', function ($query) use ($userId) {
            $query->where('user_id', '=', $userId)->where('verified', 1);
        })->orWhere('admin_id', $userId)->with(['groupUsers','admin'])->get();        
    }

    
    
    /***************************************************************************
     Next function will create or update the group object in de database
     **************************************************************************/
    
    protected function setGroup(Group $group, $data)
    {
        isset($data['name']) === true ? $group->name = $data['name'] : "";
        isset($data['admin_id']) === true ? $group->admin_id = $data['admin_id'] : "";
        return $group;
    }
    
    public function create(Array $data, $userId = "")
    {
        $group = new Group();
        $group = $this->setGroup($group, $data);
        $group->admin_id = $userId;
        $group->save();
        return $group;
    }
    
    public function update(Array $data, $groupId)
    {
        $group = $this->getGroup($groupId);
        $group = $this->setGroup($group, $data);
        $group->save();
        return $group;
    }
    
    public function delete($groupId)
    {
        $group = $this->getGroup($groupId);
        $group->delete();
    }
}
