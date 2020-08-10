<?php
namespace App\Repositories\Contracts;

use Illuminate\Http\Request;
use App\Models\Team;

interface ITeam
{
    public function getAllTeams();
    public function getTeam($id);
    public function getArrayOfSeasonUsers($seasonId);
    public function getSeasonUsers($seasonId);
    public function getArrayOfSeasons($userId);
    public function getPlayDay($seasonId, $start, $end);
    
    public function deleteTeam($teamId);
    public function saveTeam(Team $team);

}