<?php

namespace App\Services;

use App\Models\Group;


class GroupService 
{
    public function getUserGroups($userId)
    { 
        return Group::whereHas('groupUsers', function ($query) use ($userId) {
            $query->where('user_id', '=', $userId);
        })->orWhere('admin_id', $userId)->with(['groupUsers','admin'])->get();        
    }
}
