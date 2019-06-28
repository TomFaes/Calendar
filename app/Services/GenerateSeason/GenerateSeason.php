<?php

namespace App\Services\GenerateSeason;

use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IAbsence;
use App\Repositories\Contracts\ITeam;

use Illuminate\Http\Request;

use App\Models\Season;

abstract class GenerateSeason implements IGenerateSeason
{
    protected $season;
    protected $absence;
    protected $team;

    public function __construct(ISeason $seasonRepo, IAbsence $absenceRepo, ITeam $teamRepo) {
        $this->season = $seasonRepo;
        $this->absence = $absenceRepo;
        $this->team = $teamRepo;
    }

    abstract public function createPersonArray(Season $season);
    abstract public function createRandomTeam($teamArray, $gamesArray, $personArray, $teamnumber);
    abstract public function generateSeason($seasonId);
    abstract public function seasonRecap($seasonId);
    abstract protected function createAllPossibleTeams($group);
    abstract protected function removeTeam($userId, $teamArray);
    abstract protected function removeAllPlayedGames($drawTeamArray, $gamesArray, $teamnumber);
    abstract public function createJsonSeason($gamesArray, $seasonId, $numberOfTeams);
    abstract public function convertJsonToArraySeason($jsonSeason);
    abstract public function saveSeason($jsonSeason);
    

    /**
     * Get all the absence days of an User
     * @param $userId
     * @param $seasonId
     * @return array
     */
    protected function getUserAbsenceDays($userId, $seasonId){
        $absences = $this->absence->getUserAbsence($seasonId, $userId);
        $absenceArray = array();
        foreach($absences as $absence){
            $absenceArray[$absence->date] = $absence->date;
        }
        return $absenceArray;
    }
    
    /**
     * Removes all teams where a user is absence
     * @param $date
     * @param $personArray
     * @param $teamArray
     * @return array
     */
    protected function removeAbsenceTeams($date, $personArray, $teamArray){
        foreach($personArray as $person){
            if(isset($person['datumAbsent'][$date]) === true){
                $teamArray = $this->RemoveTeam($person['id'], $teamArray);
            }
        }
        return array_values($teamArray);
    }

    /**
     * add a non played gameday to the user, this will be used to make sure a user can play on a regular base
     * @param $personArray
     * @return mixed
     */
    protected function addOneNonPlayDay($personArray){
        foreach($personArray as $key => $Person){
            $personArray[$key]['nonPlayedWeeks']++;
        }
        return $personArray;
    }

    /**
     * de default next play day will be in 1 week
     * @param $seasonId
     * @param $day
     * @param $hour
     * @return mixed
     */
    public function getNextPlayDay($seasonId, $day, $hour){
        $getNow = new \Carbon\Carbon();
        $nextDate = new \Carbon\Carbon();
        $playHour = \Carbon\Carbon::parse($hour)->addHour();
        
        //if the date is bigger the the date from the season the next playday should appear
        if($getNow->format('l') == $day AND $getNow->format('H:i') > $playHour->format('H:i')){
            $nextDate->addDay(7);
        }else{
            $getNow->subDay(1);
            $nextDate->addDay(6);
        }
        //get the next play day
        $nextGameDay = $this->team->getPlayDay($seasonId, $getNow, $nextDate);
        return $nextGameDay;
    }
}