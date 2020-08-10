<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface IGroup {
    public function getGroups($itemsPerPage = 0);
    public function getGroup($id);
    
    public function getArrayOfGroupUsers($groupId);
    public function getArrayOfUserGroups($userId);
    
    public function create(Array $data, $userId = "");
    public function update(Array $data, $groupId);
    public function addUsers(Request $request, $groupId);
    public function deleteGroupUser($userId, $groupId);
    public function delete($groupId);
}
