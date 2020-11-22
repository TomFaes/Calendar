<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface IUser {
    public function getAllUsers($itemsPerPage = 0);
    public function getUser($id);
    
    public function create(Array $data);
    public function update(Array $request, $userId);
    public function updatePassword(Array $data, $userId);
    public function forgetUser($userId);
    public function delete($userId);

    public function createSocialUser($socialUser);
    public function checkEmail($email);
}
