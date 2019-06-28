<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface IUser {
    public function getAllUsers();
    public function getUser($id);
    public function getUsersFromList($listUsers);
    
    public function create(Request $request);
    public function update(Request $request, $userId);
    public function updatePassword(Request $request, $userId);
    public function delete($userId);
}
