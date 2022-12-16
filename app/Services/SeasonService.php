<?php

namespace App\Services;

use App\Models\Season;
use Carbon\Carbon;

class SeasonService 
{
    public function getActiveSeasons($userId)
    {
        $today = Carbon::now();

        return Season::whereDate('end' , '>=', $today->format('Y-m-d'))
                    ->whereDate('begin' , '<=', $today->addDays(14)->format('Y-m-d'))
                    ->whereHas('teams.group_user', function ($query) use ($userId) {
                        $query->where('user_id', '=', $userId);
                    })->get();
    }

    public function checkIfSeasonIsStarted(Season $season)
    {
        $today = Carbon::now();
        $testDate = Carbon::createFromFormat('Y-m-d', $season->begin);
        return $today->greaterThanOrEqualTo($testDate);
    }

    public function getDutchDay($date)
    {
        if($date == ""){
            return "";
        }

        $day = Carbon::parse($date)->format('l');

        switch ($day) {
            case "Monday" : $day = "ma";break;
            case "Tuesday" : $day = "di";break;
            case "Wednesday" : $day = "wo";break;
            case "Thursday" : $day = "do";break;
            case "Friday" : $day = "vr";break;
            case "Saturday" : $day = "za";break;
            case "Sunday" : $day = "zo";break;     
        }
        return $day;
    }
}
