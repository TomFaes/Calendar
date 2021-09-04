<?php

namespace App\Repositories;


use App\Repositories\Contracts\ITeam;

use Illuminate\Http\Request;

use App\Models\Team;

class TeamRepo extends Repository implements ITeam
{
    public function getAllTeams()
    {
        return Team::all();
    }

    public function getTeam($id)
    {
        return Team::find($id);
    }

    public function getAllSeasonTeams($seasonId)
    {
        return Team::where('season_id', $seasonId)->get();
    }

     /**
     * will get all users in a season
     *
     * @param  int SeasonId
     * @return array of users
     */
    public function getSeasonUsers($seasonId)
    {
        $arrayUsers = array();

        $teamUsers = team::select('group_user_id')->where('season_id', $seasonId)->groupby('group_user_id')->get();

        foreach ($teamUsers as $team) {
            if(isset($team->group_user->id ) === true){
                $arrayUsers[$team->group_user->id] = $team->group_user;
            }
        }
        return $arrayUsers;
    }

    public function getTeamsOnDate($seasonId, $date)
    {
        return Team::where('season_id', $seasonId)->where('date', $date)->get();
    }

    public function getFilledDatesInSeason($seasonId){
        return Team::where('season_id', $seasonId)->whereNotNull('group_user_id')->get();
    }

    /***************************************************************************
    Next function will create or update the group object in de database
     **************************************************************************/
    
    protected function setTeam(Team $team, $request)
    {
        $request->input('date') != "" ? $team->date = $request->input('date') : "";
        $request->input('team') != "" ? $team->team = $request->input('team') : "";
        $request->input('ask_for_replacement') != "" ? $team->player_id = $request->input('ask_for_replacement') : "";
        return $team;
    }

    public function deleteTeam($teamId)
    {
        $team = $this->getTeam($teamId);
        $seasonId = $team->season_id;
        $team->delete();
        return $seasonId;
    }

    /**
     * this will save/update a team
     * @param Team $team
     */
    public function saveTeam(Team $team)
    {
        $team->save();
        return $team;
    }

    public function askForReplacement(Team $team){
        $team->ask_for_replacement = 1;
        $team->save();
        return $team;
    }

    public function cancelRequestForReplacement(Team $team){
        $team->ask_for_replacement = 0;
        $team->save();
        return $team;
    }

    public function confirmReplacement(Team $team, $group_user_id){
        $team->group_user_id = $group_user_id;
        $team->ask_for_replacement = 0;
        $team->save();
        return $team;
    }

}