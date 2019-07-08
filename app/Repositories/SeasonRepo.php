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
     * Get all seasons from a list of season id
     * @param  int  $listSeasons: give a list of seasons seperated by ','
     * @param string $admin: if the user is an admin he can see all seasons
     * @return \Illuminate\Http\Response
     */
    public function getSeasonsFromList($listSeasons, $admin = "User"){
        if($admin == "User"){
            return Season::whereIn('id', $listSeasons)->orderBy('begin', 'asc')->where('end', '>', \Carbon\Carbon::now()->format("Y-m-d"))->get();
        }
        return Season::whereIn('id', $listSeasons)->orderBy('begin', 'asc')->get();
    }

    public function getArrayOfAdminSeasons($userId){
        $arraySeasons = array();
        $seasons = Season::select('id')->where('admin_id', $userId)->groupby('id')->get();
        foreach($seasons as $season){
            $arraySeasons[$season->id] = $season->id;
        }
        return $arraySeasons;
    }
    
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

    public function create(Request $request){
        $season = new Season();
        $season = $this->setSeason($season, $request);
        $season->save();
        return $season;
    }

    public function update(Request $request, $seasonId){
        $season = $this->getSeason($seasonId);
        $season = $this->setSeason($season, $request);
        $season->save();
        return $season;
    }

    public function delete($seasonId){
        $season = $this->getSeason($seasonId);
        $season->delete();
    }

    /**
     * Haal de dagen op met een week verschil(begin en einddatum inclusief als deze aan de voorwaarde voldoet)
     * @param $seasonId
     * @return array
     * @throws \Exception
     */
    /** this methode may be removed after the new generator system is active */
    public function get7DaySeasonDates($seasonId){
        $season = $this->getSeason($seasonId);

        $startDate = new \DateTime($season->begin);
        $endDate = new \DateTime($season->end);
        $arrayDates = array();

        while ($startDate <= $endDate) {
            $arrayDates[$startDate->format("Y-m-d")] = $startDate->format("Y-m-d");
            $startDate->add(new \DateInterval('P7D'));
        }
        return $arrayDates;
    }
}