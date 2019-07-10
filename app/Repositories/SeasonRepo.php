<?php

namespace App\Repositories;

use App\Repositories\Contracts\ISeason;
use Illuminate\Http\Request;


use App\Models\Season;

class SeasonRepo extends Repository implements ISeason
{
    public function getAllSeasons(){
        return Season::all();
    }

    public function getSeason($id){
        return Season::find($id);
    }

    /**
     * Get all seasons from a list of seasonids
     * @param  array  $listSeasons: give a list of seasons seperated by ','
     * @return object Seasons
     */
    public function getSeasonsFromList($listSeasons){
        return Season::whereIn('id', $listSeasons)->orderBy('begin', 'desc')->get();
    }

    /**
     * Get all the seasons ids where the user is a season admin
     * @param  int  $userId: 
     * @return array of seasons
     */
    public function getArrayOfAdminSeasons($userId){
        $arraySeasons = array();
        $seasons = Season::select('id')->where('admin_id', $userId)->groupby('id')->get();
        foreach($seasons as $season){
            $arraySeasons[$season->id] = $season->id;
        }
        return $arraySeasons;
    }
    
    /**
     * Get all the seasonids where the group is from a grouplist
     * @param  array  $listGroups: 
     * @return array $arraySeasons
     */
    public function getArrayOfGroupSeasons($listGroups){
        $arraySeasons = array();
        $seasons = Season::wherein('group_id', $listGroups)->get();
        foreach($seasons as $season){
            $arraySeasons[$season->id] = $season->id;
        }
        return $arraySeasons;
    }
    
    /***************************************************************************
    Next function will create or update the group object in de database
     **************************************************************************/
    
    protected function setSeason(Season $season, $request){
        $request->input('name') != "" ? $season->name = $request->input('name') : "";
        $request->input('begin') != "" ? $season->begin = $request->input('begin') : "";
        $request->input('end') != "" ? $season->end = $request->input('end') : "";
        $request->input('userId') != "" ? $season->admin_id = $request->input('userId') : "";
        $request->input('groupId') != "" ? $season->group_id = $request->input('groupId') : "";
        $request->input('begin') != "" ? $season->day = \Carbon\Carbon::parse($request->input('begin'))->format('l') : "";
        switch($season->day){
            case "Monday" : $season->day = "ma";break;
            case "Tuesday" : $season->day = "di";break;
            case "Wednesday" : $season->day = "wo";break;
            case "Thursday" : $season->day = "do";break;
            case "Friday" : $season->day = "vr";break;
            case "Saturday" : $season->day = "za";break;
            case "Sunday" : $season->day = "zo";break;     
        }
        $request->input('type') != "" ? $season->type = $request->input('type') : "";
        $request->input('hour') != "" ? $season->start_hour = $request->input('hour') : "";
        return $season;
    }

    /**
     * Create a new season
     * @param  \Illuminate\Http\Request  $request
     * @return object $season
     */
    public function create(Request $request){
        $season = new Season();
        $season = $this->setSeason($season, $request);
        $season->save();
        return $season;
    }

    /**
     * update a season
     * @param  \Illuminate\Http\Request  $request
     * @param  int $seasonId
     * @return object $season
     */
    public function update(Request $request, $seasonId){
        $season = $this->getSeason($seasonId);
        $season = $this->setSeason($season, $request);
        $season->save();
        return $season;
    }

    /**
     *  Delete a season
     * 
     * @param  int $seasonId
     * @return string
     */
    public function delete($seasonId){
        $season = $this->getSeason($seasonId);
        $season->delete();
        return "Season is deleted";
    }
}