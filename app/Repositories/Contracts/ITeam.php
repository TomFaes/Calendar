<?php
namespace App\Repositories\Contracts;

use Illuminate\Http\Request;
use App\Models\Team;

interface ITeam
{
    public function getTeam($id);
    public function getAllSeasonTeams($seasonId);
    public function getTeamsOnDate($seasonId, $date);
    public function getFilledDatesInSeason($seasonId);
    
    public function deleteTeam($teamId);
    public function deleteTeamsFromSeason($seasonId);
    public function saveTeam(Team $team);
}