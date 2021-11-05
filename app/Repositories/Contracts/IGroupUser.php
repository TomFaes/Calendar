<?php

namespace App\Repositories\Contracts;

interface IGroupUser {
    public function getAllGroupUsers($itemsPerPage = 0);
    public function getGroupUser($id);
    public function getUsersOfGroup($groupId);
    public function getGroupsOfUser($userId);

    public function create(Array $data);
    public function update(Array $data, $id);
    public function delete($Id);

    public function createCode();
    public function joinGroup($code, $userId);
    public function regenerateGroupUserCode($id);
}