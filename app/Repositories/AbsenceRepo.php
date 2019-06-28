<?php

namespace App\Repositories;


use Illuminate\Http\Request;

use App\Models\Absence;

use App\Repositories\Contracts\IAbsence;

class AbsenceRepo extends Repository implements IAbsence
{
    public function getAllAbsences(){
        return Absence::all();
    }

    public function getAbsence($id){
        return Absence::find($id);
    }

    public function getUserAbsence($seasonId, $userId){
        return Absence::where('user_id', $userId)->where('season_id', $seasonId)->orderBy('date', 'asc')->get();
    }

    public function getSeasonAbsence($seasonId){
        return Absence::where('season_id', $seasonId)->get();
    }
    
    public function getSeasonAbsenceArray($seasonId){
        $absenceArray = array();
        foreach($this->getSeasonAbsence($seasonId) as $absence){
            $absenceArray[$absence->user_id][$absence->date] = $absence->date;
        }
        return $absenceArray;
    }
    
    /***************************************************************************
    Next function will create or update the group object in de database
     **************************************************************************/

    public function create(Request $request, $seasonId, $userId){
        foreach($request['date'] AS $date){
            $absence = new Absence();
            $absence->date = $date;
            $absence->season_id = $seasonId;
            $absence->user_id = $userId;
            $absence->save();
        }
    }

    public function delete($absenceId){
        $absence = $this->getAbsence($absenceId);
        $seasonId = $absence->season_id;
        $absence->delete();
        return $seasonId;
    }
    
    public function deleteSeasonAbsence($seasonId){
        Absence::where('season_id', $seasonId)->delete();
    }
}