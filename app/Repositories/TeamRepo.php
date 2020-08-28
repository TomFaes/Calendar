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

    public function getArrayOfSeasonUsers($seasonId)
    {
        $arrayUsers = array();
        //get all players for a season
        $teamUsers = team::select('player_id')->where('season_id', $seasonId)->groupby('player_id')->get();
        foreach ($teamUsers as $user) {
            $arrayUsers[$user->player_id] = $user->player_id;
        }
        return $arrayUsers;
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
            $arrayUsers[$team->group_user->id] = $team->group_user;
        }
        return $arrayUsers;
    }
    /*
    public function getSeasonUsers($seasonId)
    {
        $arrayUsers = array();

        $teamUsers = team::select('player_id')->where('season_id', $seasonId)->groupby('player_id')->get();

        foreach ($teamUsers as $team) {
            $arrayUsers[$team->player_one->id] = $team->player_one;
        }
        return $arrayUsers;
    }
    */

    

    public function getArrayOfSeasons($userId)
    {

        $arraySeasons = array();
        $seasons = team::select('season_id')->where('player_id', $userId)->groupby('season_id')->get();
        foreach ($seasons as $season) {
            $arraySeasons[$season->season_id] = $season->season_id;
        }
        return $arraySeasons;
    }

    /**
     * @param $seasonId
     * @param $start: datetime (format 2018-07-31 10:30:00)
     * @param $end: datetime (format 2018-07-31 10:30:00)
     * @return \Illuminate\Support\Collection
     */
    public function getPlayDay($seasonId, $start, $end)
    {
        $playDay = Team::whereBetween('date', array($start, $end))->where('season_id', $seasonId)->get();
        return $playDay;
    }

    public function getTeamsOnDate($seasonId, $date)
    {
        return Team::where('season_id', $seasonId)->where('date', $date) ->get();
    }

    
    
    /***************************************************************************
    Next function will create or update the group object in de database
     **************************************************************************/
    
    protected function setTeam(Team $team, $request)
    {
        $request->input('date') != "" ? $team->date = $request->input('date') : "";
        $request->input('team') != "" ? $team->team = $request->input('team') : "";
        $request->input('playerOneId') != "" ? $team->player_id = $request->input('playerOneId') : "";
        return $team;
    }
    
    public function create(Request $request, $seasonId)
    {
        $team = new Team();
        $team->season_id = $seasonId;
        $team = $this->setTeam($team, $request);
        $team->save();
        return $team;
    }

    public function deleteTeam($teamId)
    {
        $absence = $this->getTeam($teamId);
        $seasonId = $absence->season_id;
        $absence->delete();
        return $seasonId;
    }

    /**
     * this will save a team create by the generators to the season
     * @param Team $team
     */
    public function saveTeam(Team $team)
    {
        $team->save();
    }
}