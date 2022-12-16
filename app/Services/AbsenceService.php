<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\Season;

class AbsenceService 
{
    public function getSeasonAbsenceArray(Season $season)
    {
        $seasonAbsence = Absence::where('season_id', $season->id)->get();
        $absenceArray = array();
        foreach ($seasonAbsence as $absence) {
            $absenceArray[$absence->group_user_id]['date'][] = $absence->date;
        }
        return $absenceArray;
    }
}
