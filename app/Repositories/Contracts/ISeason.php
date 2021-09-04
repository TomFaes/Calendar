<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface ISeason
{
    public function getAllSeasons();
    public function getSeason($seasonId);

    public function getActiveSeasons($userId);
    public function getGroupOfSeason($groupId);
    public function getSeasonsOfUser($userId);
    
    public function create(Array $request, $userId = "");
    public function update(Array $request, $seasonId);
    public function seasonIsGenerated($seasonId);
    public function delete($seasonId);
}