<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface ISeason
{
    public function getAllSeasons();
    public function getSeason($seasonId);
    public function getSeasonsFromList($listSeasonsArray);
    public function getArrayOfAdminSeasons($userId);
    public function getArrayOfGroupSeasons($listGroups);
    public function get7DaySeasonDates($seasonId);
    
    public function create(Request $request);
    public function update(Request $request, $seasonId);
    public function delete($seasonId);
}