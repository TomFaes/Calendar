<?php

namespace App\Services\SeasonGeneratorService;

use App\Models\Team;
use App\Models\Group;
use App\Models\Season;

use Illuminate\Http\Request;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IAbsence;


/**
    |--------------------------------------------------------------------------
    | Generate TwoFieldTwoHourThreeTeams Season
    |--------------------------------------------------------------------------
    | TwoFieldTwoHourThreeTeams season: A season is created based on 2 courts for 2 hours and there will be 3 teams,
    |
    | 1st Field: 2 hours double
    | 2nd Field: 2 hours single
    |
    | 3 teams will be created 1 & 2 will play 1 hour single, team 3 will play 2 hours double
    | teams 1 and 2 will switch each hour
    | 
    | conditions:
    | - the time between play dates shouldn't be more the 2 weeks(with the exception if players are absence)
    | - Everyone should play atleast once with everyone in team 1 or 2
    | - Players shouldn't be in the same team with a player twice
    |exception:
    | - if the season is really long then it is possible to play more then once in the same team
    | - If there are alot of absence people on the same day it is possible to play more then once in the same team
    */
class TwoFieldTwoHourThreeTeams implements IGenerator
{

    /** @var App\Repositories\Contracts\ISeason */
    protected $season;
    /** @var App\Repositories\Contracts\IAbsence */
    protected $absence;
    /** @var App\Repositories\Contracts\ITeam */
    protected $team;

    public function __construct(ISeason $seasonRepo, IAbsence $absenceRepo, ITeam $teamRepo) 
    {
        $this->season = $seasonRepo;
        $this->absence = $absenceRepo;
        $this->team = $teamRepo;
    }
   
    /**
     * Generates the season
     *
     * @param  int  $seasonId
     * @return json
     */
    public function generateSeason(Season $season)
    {
        //create all possible teams
        $allPossibleTeamArray = $this->createAllPossibleTeams($season->group);

        $gamesArray = array();

        //create the personArray to store data for the season
        $gamesArray['person'] = $this->createPersonStats($season);

        //get all date for the season
        $seasonDates = $this->getPlayDates($season->begin, $season->end);

        foreach ($seasonDates as $seasonDate) {
            $key = $seasonDate['date'];
            //Gameday
            $gamesArray['season'][$key]['datum'] = $seasonDate['date'];

            //this team array will be used to remove played teams and absence teams
            $gamesArray['allTeams'] = $allPossibleTeamArray;
            $gamesArray['person'] = $this->addOneNonPlayDay($gamesArray['person']);

            $gamesArray = $this->createDayTeams($seasonDate, $gamesArray);
        }
        return $this->createJsonSeason($gamesArray['season'], $season, 3);
    }

    /**
     * Returns the weekly play dates
     *
     * @param  date  $beginDate
     * @param  date  $endDate
     * @return Array
     */
    public function getPlayDates($beginDate, $endDate)
    {
        $startDate = new \DateTime($beginDate);
        $endDate = new \DateTime($endDate);
        $arrayDates = array();

        while ($startDate <= $endDate) {
            $arrayDates[]['date'] = $startDate->format("Y-m-d");
            $startDate->add(new \DateInterval('P7D'));
        }
        return $arrayDates;
    }

    /**
     * returns the season Calendar
     *
     * @param  object Season
     * @return json
     */
    public function getSeasonCalendar(Season $season)
    {
        $gamesArray = array();

        foreach ($season->teams as $key=>$team) {
            $date = $team->date;
            $teamnumber = $team->team;
            $playerId = $team->group_user_id;
            
            //Gameday
            $gamesArray[$date]['datum'] = $date;
            $gamesArray[$date][$teamnumber][$playerId]['teamId'] = $team->id;;
            if (isset($gamesArray[$date][$teamnumber]['player1']) === false) {
                $gamesArray[$date][$teamnumber]['player1'] = $playerId;
            } else {
                $gamesArray[$date][$teamnumber]['player2'] = $playerId;
            }      
            $gamesArray[$date]['teamIds'][$team->id]['teamId'] = $team->id;
            $gamesArray[$date]['teamIds'][$team->id]['team'] = $teamnumber;
            $gamesArray[$date]['teamIds'][$team->id]['groupUserId'] = $playerId;
            $gamesArray[$date]['teamIds'][$team->id]['replacement'] = $team->ask_for_replacement;
        }
        //return $gamesArray;
        return $this->createJsonSeason($gamesArray, $season);
    }

     /**
     * Saves all the teams from the json file to the database
     * make sure there is a data set
     * @param $jsonSeason
     */
    public function saveSeason($jsonSeason)
    {
        $jsonArray = json_decode($jsonSeason);
        foreach ($jsonArray->data As $day) {
            foreach ($day->user As $users) {
                if($users->team == ""){
                    continue;
                }
                $team = new Team();
                $team->season_id = $jsonArray->seasonData->id;
                $team->date = $day->day;
                $team->team = $users->team;
                $team->group_user_id = $users->groupUser;
                $this->team->saveTeam($team);
            }
        }
    }

    /**
     * create the season day
     * @param $seasonDate
     * @param array $gamesArray;
     * @return array
     */
    protected function createDayTeams($seasonDate, Array $gamesArray)
    {
         //Every gameday there is a new random draw for teams
        //This is done to avoid the same players will always be in the first match if the backup needs to be used
        $teamShuffle = [1,2,3];
        shuffle($teamShuffle);
        foreach ($teamShuffle AS $number) {
            //teams will be called team + a number
            $teamnumber = "team".$number;
            $date = $seasonDate['date'];

            //prepare the draw error to see which teams will be available for the draw
            $drawTeamArray = $this->removeAbsenceTeams($date, $gamesArray['person'], $gamesArray['allTeams']);
            $drawTeamArray = $this->removeAllPlayedGames($drawTeamArray, $gamesArray['season'], $teamnumber);
            //prepare the backup array that allows players to be with previous played persons
            $backupTeamArray = $this->removeAbsenceTeams($date, $gamesArray['person'], $gamesArray['allTeams']);

            //choose a random team from the drawArray
            if (count($drawTeamArray) > 0) {
                $random = $this->createRandomTeam($drawTeamArray, $gamesArray['person'], $teamnumber);
                $player1 = isset($drawTeamArray[$random]['player1']) === true ? $drawTeamArray[$random]['player1'] : "";
                $player2 = isset($drawTeamArray[$random]['player2']) === true ? $drawTeamArray[$random]['player2'] : "";
            }

            //choose a random team from the backup
            if (($player1 == "" OR $player2 == "") AND count($backupTeamArray) > 0) {
                $random = $this->createRandomTeam($backupTeamArray, $gamesArray['season'], $gamesArray['person'], $teamnumber);
                $player1 = isset($drawTeamArray[$random]['player1']) === true ? $drawTeamArray[$random]['player1'] : "";
                $player2 = isset($drawTeamArray[$random]['player2']) === true ? $drawTeamArray[$random]['player2'] : "";
            }

            //if there are no 2 players then go to next team shuffle
            if ($player1 == "" OR $player2 == "") {
                continue;
            }

            //remove the team to exclude it from the next draw for the other teams
            $gamesArray['allTeams'] = $this->RemoveTeam($player1, $gamesArray['allTeams']);
            $gamesArray['allTeams'] = $this->RemoveTeam($player2, $gamesArray['allTeams']);

            //add one to the team the players that has played
            $gamesArray['person'][$player1][$teamnumber]++;
            $gamesArray['person'][$player2][$teamnumber]++;

            //add games to the player stats
            $gamesArray['person'][$player1]["totalGames"]++;
            $gamesArray['person'][$player2]["totalGames"]++;

            if ($teamnumber != "team3") {
                $gamesArray['person'][$player1]['against'][$player1]['name'] = $gamesArray['person'][$player1]['name'];
                $gamesArray['person'][$player1]['against'][$player2]['name'] = $gamesArray['person'][$player2]['name'];
                $gamesArray['person'][$player2]['against'][$player1]['name'] = $gamesArray['person'][$player1]['name'];
                $gamesArray['person'][$player2]['against'][$player2]['name'] = $gamesArray['person'][$player2]['name'];
            }

            //set the counter to 0 for non played weeks
            $gamesArray['person'][$player1]['nonPlayedWeeks'] = 0;
            $gamesArray['person'][$player2]['nonPlayedWeeks'] = 0;

            //Add team to playday
            $gamesArray['season'][$date][$teamnumber]['player1'] = $player1;
            $gamesArray['season'][$date][$teamnumber]['player2'] = $player2;

            //controle number to see if there are 6 players each week
            if (isset($gamesArray['season'][$date]['gameCounter'])  === false) {
                $gamesArray['season'][$date]['gameCounter'] = 0;
            }
            $gamesArray['season'][$date]['gameCounter'] +=2;
        }
        return $gamesArray;
    }

    /**
     * create all teams that can be created from the group of people that is given
     * @param $group
     * @return array
     */
    protected function createAllPossibleTeams(Group $group)
    {
        $teamArray = array();
        $z = 0;
        foreach ($group->groupUsers as $user1) {
            foreach ($group->groupUsers as $user2) {
                if ($user1->id < $user2->id) {
                    $teamArray[$z]['player1'] = $user1->id;
                    $teamArray[$z]['player2'] = $user2->id;
                    $z++;
                }
            }
        }
        return $teamArray;
    }

    /**
     * set the user Array with some default data
     * @param Season $season
     * @return array
     */
    protected function createPersonStats(Season $season)
    {
        $personStat = array();
        foreach ($season->group->groupUsers as $groupUser) {
            $personStat[$groupUser->id]['id'] = $groupUser->id;
            $personStat[$groupUser->id]['name'] = $groupUser->firstname." ".$groupUser->name;
            $personStat[$groupUser->id]['totalGames'] = 0;
            $personStat[$groupUser->id]['datumAbsent'] = $this->getUserAbsenceDays($groupUser->id, $season->id);
            $personStat[$groupUser->id]['team1'] = 0;
            $personStat[$groupUser->id]['team2'] = 0;
            $personStat[$groupUser->id]['team3'] = 0;
            $personStat[$groupUser->id]['nonPlayedWeeks'] = 0;
        }
        return $personStat;
    }

    /**
     * Return all absences of a user in a season
     *
     * @param  int  $userId
     * @param  int  $seasonId
     * @return Array
     */
    protected function getUserAbsenceDays($groupUserId, $seasonId)
    {
        $absences = $this->absence->getUserAbsence($seasonId, $groupUserId);
        $absenceArray = array();
        foreach ($absences as $absence) {
            $absenceArray[$absence->date] = $absence->date;
        }
        return $absenceArray;
    }

    /**
     * add a non played gameday to the user, this will be used to make sure a user can play on a regular base
     * @param $personArray
     * @return mixed
     */
    protected function addOneNonPlayDay($personArray)
    {
        foreach ($personArray as $key => $Person) {
            $personArray[$key]['nonPlayedWeeks']++;
        }
        return $personArray;
    }

    /**
     * Removes all teams where a user is absence
     * @param $date
     * @param $personArray
     * @param $teamArray
     * @return array
     */
    protected function removeAbsenceTeams($date, $personArray, $teamArray)
    {
        foreach ($personArray as $person) {
            if (isset($person['datumAbsent'][$date]) === false) {
                continue;
            }
            $teamArray = $this->RemoveTeam($person['id'], $teamArray);
        }
        return array_values($teamArray);
    }

    /**
     * Return all teams that haven't played
     *
     * @param  Array  $drawTeamArray
     * @param  Array  $gamesArray
     * @param  string $teamnumber
     * @return Array
     */
    protected function removeAllPlayedGames($drawTeamArray, $gamesArray, $teamnumber)
    {
        //to do after creating the first games
        foreach ($gamesArray as $key=>$games) {
            if (isset($games[$teamnumber]['player1']) === false OR isset($games[$teamnumber]['player2']) === false) {
                continue;
            }

            foreach ($drawTeamArray AS $key2=>$drawTeam) {
                $player1 = isset($drawTeam['player1']) === true ? $drawTeam['player1'] : "";
                $player2 = isset($drawTeam['player2']) === true ? $drawTeam['player2'] : "";
                if ($games[$teamnumber]['player1'] == $player1 AND $games[$teamnumber]['player2'] == $player2) {
                    unset($drawTeamArray[$key2]);
                }
            }
        }
        return array_values($drawTeamArray);
    }

     /**
     * create a team meeting the requirements of the class
     * @param $teamArray
     * @param $personArray
     * @param $teamNumber
     * @return int
     */
    protected function createRandomTeam($teamArray, $personArray, $teamNumber)
    {
        $lowestGames = $lowestTeamPlays = 100;
        $highestGames = $highestTeamPlays = 0;

        //check the lowest and highest games and teammatches
        foreach ($personArray as $person) {
            $person['totalGames'] < $lowestGames ? $lowestGames = $person['totalGames'] : "";
            $person['totalGames'] > $highestGames ? $highestGames = $person['totalGames'] : "";

            $person[$teamNumber] < $lowestTeamPlays ? $lowestTeamPlays = $person[$teamNumber] : "";
            $person[$teamNumber] > $highestTeamPlays ? $highestTeamPlays = $person[$teamNumber] : "";
        }
        //if lowest and highest are the same add one to the total
        ($lowestGames+1) > $highestGames ? $highestGames++ : "";
        ($lowestTeamPlays+1) > $highestTeamPlays ? $highestTeamPlays++ : "";

        //this loop will determen if de team meets all the required conditions
        for ($x=0;$x<200;$x++) {
            //this loop will determen if the player hasn't had a long pause
            for ($z=0;$z<50;$z++) {
                //this loop will determen if the persons hasn't been in the same team;
                for ($y=0;$y<50;$y++) {
                    //determen a random team
                    $random = rand(0, count($teamArray)-1);
                    $teamPlayer1 = isset($teamArray[$random]['player1']) === true ? $teamArray[$random]['player1'] : 9999999;
                    $teamPlayer2 = isset($teamArray[$random]['player2']) === true ? $teamArray[$random]['player2'] : 9999999;

                    if($teamPlayer1 === 9999999 OR $teamPlayer2 === 9999999){
                        continue;
                    }

                    //if players haven't played for more then 1 week then break the loop
                    if($personArray[$teamPlayer1]['nonPlayedWeeks'] > 1 OR $personArray[$teamPlayer2]['nonPlayedWeeks'] > 1){
                        break;
                    }
                }
                //if player1 or player2 hasn't played for more then 2 weeks the loop will break and go on to the next controle
                if (isset($personArray[$teamPlayer1]['against'][$teamPlayer2]['name']) === false AND isset($personArray[$teamPlayer2]['against'][$teamPlayer1]['name']) === false) {
                    break;
                }
            }
            //check if the meet the required highest games and highest team plays
            if($teamPlayer1 === 9999999 OR $teamPlayer2 === 9999999){
                continue;
            }

            if ($personArray[$teamPlayer1]['totalGames'] < $highestGames AND $personArray[$teamPlayer2]['totalGames'] < $highestGames AND $personArray[$teamPlayer1][$teamNumber] < $highestTeamPlays AND $personArray[$teamPlayer2][$teamNumber] < $highestTeamPlays) {
                break;
            }
        }
        return $random;
    }

    /**
     * Removes all possible teams of a given user
     * @param $userId
     * @param $teamArray
     * @return array
     */
    protected function removeTeam($userId, $teamArray)
    {
        foreach ($teamArray as $key=>$team) {
            if ($userId == $team['player1'] OR $userId == $team['player2']) {
                unset($teamArray[$key]);
            }
        }
        return array_values($teamArray);
    }

    /**
     * this function will create a json of the season. This will be sent to the screen so when the season is ok it can be saved in an easy way.
     * The json should always have the same structure so it can be saved in the same way if there are more generators
     * @param $gamesArray
     * @param $seasonId
     * @return string
     */
    protected function createJsonSeason($gamesArray, Season $season)
    {
        $getNow = new \Carbon\Carbon();
        $getNow->addDay(-1);
        $nextDate = new \Carbon\Carbon();
        $nextDate->addDay(14);

        $arrayJson = array();
        $arrayJson['seasonData']  = $season;
        $arrayJson['absenceData'] = $this->absence->getSeasonAbsenceArray($season->id);
        $arrayJson['groupUserData'] = $this->team->getSeasonUsers($season->id);
        $arrayJson['generateGroupUserData'] =  $season->group->groupUsers;

        $x=0;
        $y=0;
        foreach ($gamesArray as $game) {
            $datum =  $game['datum'];

            $prepareGroupUser = $arrayJson['generateGroupUserData'] ;
            if(count($arrayJson['groupUserData']) > 0){
                $prepareGroupUser = $arrayJson['groupUserData'];
            }
            
            foreach($prepareGroupUser AS $groupUser){
                $arrayJson['data'][$y]['user'][$groupUser['id']]['groupUser'] = $groupUser['id'];
                $arrayJson['data'][$y]['user'][$groupUser['id']]['team'] = "";
                $arrayJson['data'][$y]['user'][$groupUser['id']]['teamId'] ="";
                $arrayJson['data'][$y]['user'][$groupUser['id']]['replacement'] = 0;
            }
            
            if ($getNow <= \Carbon\Carbon::parse($datum) && $nextDate >= \Carbon\Carbon::parse($datum)) {
                $arrayJson['currentPlayDay'] = $y;
                $nextDate->addDay(-14);
            }

            for ($z=1;$z<=3;$z++) {
                $team = 'team'.$z;
                $teamplayerOne = isset($game[$team]['player1']) === true ? $game[$team]['player1'] : "";
                $teamplayerTwo = isset($game[$team]['player2']) === true ? $game[$team]['player2'] : "";

                if ($team != "team3") {
                    if (isset($arrayJson['stats'][$teamplayerOne]['against'][$teamplayerTwo]) === false) {
                        isset($arrayJson['stats'][$teamplayerOne]['countAgainst']) === true ? $arrayJson['stats'][$teamplayerOne]['countAgainst']++ : $arrayJson['stats'][$teamplayerOne]['countAgainst'] = 1;
                    }
                    if (isset($arrayJson['stats'][$teamplayerTwo]['against'][$teamplayerOne]) === false) {
                        isset($arrayJson['stats'][$teamplayerTwo]['countAgainst']) === true ? $arrayJson['stats'][$teamplayerTwo]['countAgainst']++ : $arrayJson['stats'][$teamplayerTwo]['countAgainst'] = 1;
                    }

                    $arrayJson['stats'][$teamplayerOne]['against'][$teamplayerTwo] = $teamplayerTwo;
                    $arrayJson['stats'][$teamplayerTwo]['against'][$teamplayerOne] = $teamplayerOne;
                }

                isset($arrayJson['stats'][$teamplayerOne][$team]) === true ? $arrayJson['stats'][$teamplayerOne][$team]++ : $arrayJson['stats'][$teamplayerOne][$team] = 1;
                isset($arrayJson['stats'][$teamplayerTwo][$team]) === true ? $arrayJson['stats'][$teamplayerTwo][$team]++ : $arrayJson['stats'][$teamplayerTwo][$team] = 1;
                isset($arrayJson['stats'][$teamplayerOne]['total']) === true ? $arrayJson['stats'][$teamplayerOne]['total']++ : $arrayJson['stats'][$teamplayerOne]['total'] = 1;
                isset($arrayJson['stats'][$teamplayerTwo]['total']) === true ? $arrayJson['stats'][$teamplayerTwo]['total']++ : $arrayJson['stats'][$teamplayerTwo]['total'] = 1;
                
                $x++;
                $teamId = 0;
                $arrayJson['data'][$y]['day'] = $datum;
                if($teamplayerOne > 0){
                    $teamId = isset($game[$team][$teamplayerOne]['teamId']) === true ? $game[$team][$teamplayerOne]['teamId'] : "";

                    $arrayJson['data'][$y]['user'][$teamplayerOne]['groupUser'] = $teamplayerOne;
                    $arrayJson['data'][$y]['user'][$teamplayerOne]['team'] = $team;
                    $arrayJson['data'][$y]['user'][$teamplayerOne]['teamId'] = $teamId;
                    if(isset($game['teamIds'][$teamId]['replacement']) === true){
                        $arrayJson['data'][$y]['user'][$teamplayerOne]['replacement']  = $game['teamIds'][$teamId]['replacement'];
                    }
                }

                if($teamplayerTwo > 0){
                    $teamId = isset($game[$team][$teamplayerTwo]['teamId']) === true ? $game[$team][$teamplayerTwo]['teamId'] : "";

                    $arrayJson['data'][$y]['user'][$teamplayerTwo]['groupUser'] = $teamplayerTwo;
                    $arrayJson['data'][$y]['user'][$teamplayerTwo]['team'] = $team;
                    $arrayJson['data'][$y]['user'][$teamplayerTwo]['teamId'] = $teamId;
                    if(isset($game['teamIds'][$teamId]['replacement']) === true){
                        $arrayJson['data'][$y]['user'][$teamplayerTwo]['replacement']  = $game['teamIds'][$teamId]['replacement'];
                    }
                }
                /** end new array to build the calendar */

            }
            /** new array to build the calendar */
            $arrayJson['data'][$y]['day'] = $datum;
            if(isset($game['teamIds']) === true){
                foreach($game['teamIds'] AS $teamIds){
                    $teamId = $teamIds['teamId'];
                    $arrayJson['data'][$y]['teams'][$teamId]['teamId'] = $teamId;
                    $arrayJson['data'][$y]['teams'][$teamId]['team'] = $teamIds['team'];
                    $arrayJson['data'][$y]['teams'][$teamId]['groupUserId'] = $teamIds['groupUserId'];
                }
            }            
             /** end new array to build the calendar */
            $y++;
        }
        return $arrayJson;
    }
}