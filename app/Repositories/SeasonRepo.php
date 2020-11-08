<?php

namespace App\Repositories;

use App\Repositories\Contracts\ISeason;
use Illuminate\Http\Request;


use App\Models\Season;
use Carbon\Carbon;

class SeasonRepo extends Repository implements ISeason
{
    public function getAllSeasons()
    {
        return Season::all();
    }

    public function getSeason($id)
    {
        return Season::with(['group'])->find($id);
    }

    /**
     * Get all seasons from a list of seasonids
     * @param  array  $listSeasons: give a list of seasons seperated by ','
     * @return object Seasons
     */
    public function getSeasonsFromList($listSeasons)
    {
        return Season::whereIn('id', $listSeasons)->orderBy('begin', 'desc')->get();
    }

    public function getActiveSeasons($userId)
    {
        $today = $mytime = Carbon::now();

        return Season::whereDate('end' , '>=', $today->format('Y-m-d'))
        ->whereDate('begin' , '<=', $today->addDays(14)->format('Y-m-d'))
        ->whereHas('teams.group_user', function ($query) use ($userId) {
            $query->where('user_id', '=', $userId);
        })->get();
    }

    public function getGroupOfSeason($groupId)
    {
        return Season::where('group_id', $groupId)->get();
    }

    public function getSeasonsOfUser($userId)
    {

        return Season::whereHas('teams.group_user', function ($query) use ($userId) {
            $query->where('user_id', '=', $userId);
        })
        ->orwhereHas('group.groupUsers', function ($query) use ($userId) {
            $query->where('user_id', '=', $userId);
        })
        ->orWhere('admin_id', $userId)->with(['group', 'admin', 'group.groupUsers'])->get();
    }
    
    /***************************************************************************
    Next function will create or update the group object in de database
     **************************************************************************/
    
    protected function setSeason(Season $season, $data)
    {
        isset($data['name']) === true ? $season->name = $data['name'] : "";
        isset($data['begin']) === true ? $season->begin = $data['begin'] : "";
        isset($data['end']) === true ? $season->end = $data['end'] : "";
        isset($data['userId']) === true ? $season->admin_id = $data['userId'] : "";
        isset($data['groupId']) === true ? $season->group_id = $data['groupId'] : "";
        isset($data['name']) === true ? $season->name = $data['name'] : "";
        isset($data['begin']) != "" ? $season->day = \Carbon\Carbon::parse($data['begin'] )->format('l') : "";
        
        switch ($season->day) {
            case "Monday" : $season->day = "ma";break;
            case "Tuesday" : $season->day = "di";break;
            case "Wednesday" : $season->day = "wo";break;
            case "Thursday" : $season->day = "do";break;
            case "Friday" : $season->day = "vr";break;
            case "Saturday" : $season->day = "za";break;
            case "Sunday" : $season->day = "zo";break;     
        }

        isset($data['type']) === true ? $season->type = $data['type'] : "";
        isset($data['hour']) === true ? $season->start_hour = $data['hour'] : "";
        isset($data['public']) === true ? $season->public = $data['public'] : "";
        return $season;
    }

    /**
     * Create a new season
     * @param  \Illuminate\Http\Request  $request
     * @return object $season
     */
    public function create(Array $data, $userId = "")
    {
        $season = new Season();
        $season = $this->setSeason($season, $data);
        $season->admin_id = $userId;
        $season->save();
        return $season;
    }

    /**
     * update a season
     * @param  \Illuminate\Http\Request  $request
     * @param  int $seasonId
     * @return object $season
     */
    public function update(Array $data, $seasonId)
    {
        $season = $this->getSeason($seasonId);
        $season = $this->setSeason($season, $data);
        $season->save();
        return $season;
    }

    /**
     *  Delete a season
     * 
     * @param  int $seasonId
     * @return string
     */
    public function delete($seasonId)
    {
        $season = $this->getSeason($seasonId);
        $season->delete($seasonId);
        return "Season is deleted";
    }
}