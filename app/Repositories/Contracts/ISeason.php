<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface ISeason
{
    public function getAllSeasons();
    public function getSeason($seasonId);
    public function getSeasonsFromList($listSeasonsArray);

    public function getSeasonsOfUser($userId);
    public function getGroupOfSeason($groupId);
    
    public function create(Array $request, $userId = "");
    public function update(Array $request, $seasonId);
    public function delete($seasonId);
}