<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface IGroup {
    public function getAllGroups();
    public function getGroup($id);
    public function getArrayOfGroupUsers($groupId);
    public function getArrayOfUserGroups($userId);
    
    public function create(Request $request);
    public function update(Request $request, $groupId);
    public function addUsers(Request $request, $groupId);
    public function deleteGroupUser($userId, $groupId);
    public function delete($groupId);
}
