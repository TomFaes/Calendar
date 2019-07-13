<?php

namespace App\Services\SeasonGeneratorService;

use App\Models\Season;
use App\Models\Team;
use Illuminate\Http\Request;

use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IAbsence;
use App\Repositories\Contracts\ITeam;


/*
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
class TwoFieldTwoHourThreeTeams implements IGenerator{

    /** @var App\Repositories\Contracts\ISeason */
    protected $season;
    /** @var App\Repositories\Contracts\IAbsence */
    protected $absence;
    /** @var App\Repositories\Contracts\ITeam */
    protected $team;

    public function __construct(ISeason $seasonRepo, IAbsence $absenceRepo, ITeam $teamRepo) {
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
    public function generateSeason($seasonId){
        $season = $this->season->getSeason($seasonId);

        //create all possible teams
        $allPossibleTeamArray = $this->createAllPossibleTeams($season->group);

        //create the personArray to store data for the season
        $personArray = $this->createPersonStats($season);

        //get all date for the season
        $seasonDates = $this->getPlayDates($season->begin, $season->end);

        $gamesArray = array();

        foreach($seasonDates as $key=>$seasonDate){
            //Gameday
            $gamesArray[$key]['datum'] = $seasonDate;

            //Every gameday there is a new random draw for teams
            //This is done to avoid the same players will always be in the first match if the backup needs to be used
            $teamShuffle = [1,2,3];
            shuffle($teamShuffle);

            //this team array will be used to remove played teams and absence teams
            $teamArray = $allPossibleTeamArray;
            $personArray = $this->addOneNonPlayDay($personArray);

            foreach($teamShuffle AS $number){
                //teams will be called team + a number
                $teamnumber = "team".$number;

                //prepare the draw error to see which teams will be available for the draw
                $drawTeamArray = $this->removeAbsenceTeams($seasonDate, $personArray, $teamArray);
                $drawTeamArray = $this->removeAllPlayedGames($drawTeamArray, $gamesArray, $teamnumber);
                 //prepare the backup array that allows players to be with previous played persons
                 $backupTeamArray = $this->removeAbsenceTeams($seasonDate, $personArray, $teamArray);

                 //choose a random team from the drawArray
                if(count($drawTeamArray) > 0){
                    $random = $this->createRandomTeam($drawTeamArray, $personArray, $teamnumber);
                }

                //set the player 1 and 2 id
                if(isset($drawTeamArray[$random]['player1']) === true AND isset($drawTeamArray[$random]['player2'])){
                    $player1 = $drawTeamArray[$random]['player1'];
                    $player2 = $drawTeamArray[$random]['player2'];
                }else{
                    if(count($backupTeamArray) > 0){
                        $random = $this->createRandomTeam($backupTeamArray, $gamesArray, $personArray, $teamnumber);
                    }
                    //set the player 1 and 2 id
                    $player1 = isset($drawTeamArray[$random]['player1']) === true ? $drawTeamArray[$random]['player1'] : "";
                    $player2 = isset($drawTeamArray[$random]['player2']) === true ? $drawTeamArray[$random]['player2'] : "";
                }

                //If there are 2 players then add the player to the gameday
                if($player1 != "" AND $player2 != ""){
                    //remove the team to exclude it from the next draw for the other teams
                    $teamArray = $this->RemoveTeam($player1, $teamArray);
                    $teamArray = $this->RemoveTeam($player2, $teamArray);

                    //add one to the team the players that has played
                    $personArray[$player1][$teamnumber]++;
                    $personArray[$player2][$teamnumber]++;

                    //add games to the player stats
                    $personArray[$player1]["totalGames"]++;
                    $personArray[$player2]["totalGames"]++;

                    if($teamnumber != "team3"){
                        $personArray[$player1]['against'][$player1]['name'] = $personArray[$player1]['name'];
                        $personArray[$player1]['against'][$player2]['name'] = $personArray[$player2]['name'];
                        $personArray[$player2]['against'][$player1]['name'] = $personArray[$player1]['name'];
                        $personArray[$player2]['against'][$player2]['name'] = $personArray[$player2]['name'];
                    }
                    //set the counter to 0 for non played weeks
                    $personArray[$player1]['nonPlayedWeeks'] = 0;
                    $personArray[$player2]['nonPlayedWeeks'] = 0;

                     //Add team to playday
                     $gamesArray[$key][$teamnumber]['player1'] = $player1;
                     $gamesArray[$key][$teamnumber]['player2'] = $player2;

                     //controle number to see if there are 6 players each week
                    isset($gamesArray[$key]['gameCounter']) === true ? $gamesArray[$key]['gameCounter'] +=2 : $gamesArray[$key]['gameCounter'] = 2;
                }
            }
        }
        return $this->createJsonSeason($gamesArray,$seasonId, 3);
    }

    /**
     * Returns the weekly play dates
     *
     * @param  date  $beginDate
     * @param  date  $endDate
     * @return Array
     */
    public function getPlayDates($beginDate, $endDate){
        $startDate = new \DateTime($beginDate);
        $endDate = new \DateTime($endDate);
        $arrayDates = array();

        while ($startDate <= $endDate) {
            $arrayDates[$startDate->format("Y-m-d")] = $startDate->format("Y-m-d");
            $startDate->add(new \DateInterval('P7D'));
        }
        return $arrayDates;
    }

    /**
     * Get the next play day, default is 1 week if the season is busy otherwise the first date of the season will be taken
     * @param object Season $season
     * @return array
     */
    //public function getNextPlayDay(Season $season, $day, $hour){
    public function getNextPlayDay(Season $season){
        $returndata = array();
        $getNow = new \Carbon\Carbon();
        $nextDate = new \Carbon\Carbon();
        $playHour = \Carbon\Carbon::parse($season->start_hour)->addHour();

        //if the date is bigger then the date and hour from the season the next playday should appear
        if($getNow->format('l') == $season->day AND $getNow->format('H:i') > $playHour->format('H:i')){
            $nextDate->addDay(7);
        }else{
            $getNow->subDay(1);
            $nextDate->addDay(6);
        }
        //get the next play day
        $nextGameDay = $this->team->getPlayDay($season->id, $getNow, $nextDate);

        //if nextday is 0 then get the startdate if that is bigger then the current date
        if(count($nextGameDay) == 0) {
            if($season->begin > \Carbon\Carbon::now()->format("Y-m-d")){
                $nextGameDay = $this->team->getTeamsOnDate($season->id, $season->begin);
            }
        }

        //create the display data
        if(count($nextGameDay) > 0) {
            $returndata['date'] = $nextGameDay[0]->date;
            $returndata['season'] = $season;
            $returndata['users'] = $season->group->users;
            foreach($nextGameDay as $teams){
                $returndata['display'][$teams->player_id]  =  substr($teams->team, -1);
            }
            foreach($this->absence->getSeasonAbsence($season->id)->toArray() as $absence){
                $returndata['absenceDays'][$absence['user_id']][$absence['date']] = $absence['date'];
            }
        }
        return $returndata;
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

        foreach($season->teams as $key=>$team){
            $date = $team->date;
            $teamnumber = $team->team;
            $playerId = $team->player_id;
            
            //Gameday
            $gamesArray[$date]['datum'] = $date;
            if(isset($gamesArray[$date][$teamnumber]['player1'] ) === false){
                $gamesArray[$date][$teamnumber]['player1'] = $playerId;
            }else{
                $gamesArray[$date][$teamnumber]['player2'] = $playerId;
            }           
        }
        return $this->createJsonSeason($gamesArray, $season->id);
    }

     /**
     * Saves all the teams from the json file to the database
     * make sure there is a data set
     * @param $jsonSeason
     */
    public function saveSeason($jsonSeason){
        $jsonArray = json_decode($jsonSeason);
        foreach($jsonArray->data As $teams){
            if($teams->user_id1 > 0 AND $teams->user_id2 > 0){
                $team = new Team();
                $team->season_id = $teams->seasonId;
                $team->date = $teams->date;
                $team->team = $teams->team;
                $team->player_id = $teams->user_id1;
                $this->team->saveTeam($team);

                $team1 = new Team();
                $team1->season_id = $teams->seasonId;
                $team1->date = $teams->date;
                $team1->team = $teams->team;
                $team1->player_id = $teams->user_id2;
                $this->team->saveTeam($team1);
            }
        }
    }

    /**
     * create all teams that can be created from the group of people that is given
     * @param $group
     * @return array
     */
    protected function createAllPossibleTeams($group){
        $teamArray = array();
        $z = 0;
        foreach($group->users as $user1){
            foreach($group->users as $user2){
                if($user1->id < $user2->id){
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
    protected function createPersonStats(Season $season){
        $personStat = array();
        foreach($season->group->users as $user){
            $personStat[$user->id]['id'] = $user->id;
            $personStat[$user->id]['name'] = $user->firstname." ".$user->name;
            $personStat[$user->id]['totalGames'] = 0;
            $personStat[$user->id]['datumAbsent'] = $this->getUserAbsenceDays($user->id, $season->id);
            $personStat[$user->id]['team1'] = 0;
            $personStat[$user->id]['team2'] = 0;
            $personStat[$user->id]['team3'] = 0;
            $personStat[$user->id]['nonPlayedWeeks'] = 0;
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
    protected function getUserAbsenceDays($userId, $seasonId){
        $absences = $this->absence->getUserAbsence($seasonId, $userId);
        $absenceArray = array();
        foreach($absences as $absence){
            $absenceArray[$absence->date] = $absence->date;
        }
        return $absenceArray;
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
     * Return all absences of a user in a season
     *
     * @param  Array  $drawTeamArray
     * @param  Array  $gamesArray
     * @param  string $teamnumber
     * @return Array
     */
    protected function removeAllPlayedGames($drawTeamArray, $gamesArray, $teamnumber){
        //to do after creating the first games
        foreach($gamesArray as $key=>$games){
            if(isset($games[$teamnumber]['player1']) === true AND isset($games[$teamnumber]['player2']) === true){
                foreach($drawTeamArray AS $key2=>$drawTeam){
                    $player1 = isset($drawTeam['player1']) === true ? $drawTeam['player1'] : "";
                    $player2 = isset($drawTeam['player2']) === true ? $drawTeam['player2'] : "";
                    if($games[$teamnumber]['player1'] == $player1 AND $games[$teamnumber]['player2'] == $player2){
                        unset($drawTeamArray[$key2]);
                    }
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
    protected function createRandomTeam($teamArray, $personArray, $teamNumber){
        $lowestGames = $lowestTeamPlays = 100;
        $highestGames = $highestTeamPlays = 0;

        //check the lowest and highest games and teammatches
        foreach($personArray as $person){
            $person['totalGames'] < $lowestGames ? $lowestGames = $person['totalGames'] : "";
            $person['totalGames'] > $highestGames ? $highestGames = $person['totalGames'] : "";

            $person[$teamNumber] < $lowestTeamPlays ? $lowestTeamPlays = $person[$teamNumber] : "";
            $person[$teamNumber] > $highestTeamPlays ? $highestTeamPlays = $person[$teamNumber] : "";
        }
        //if lowest and highest are the same add one to the total
        ($lowestGames+1) > $highestGames ? $highestGames++ : "";
        ($lowestTeamPlays+1) > $highestTeamPlays ? $highestTeamPlays++ : "";

        //this loop will determen if de team meets all the required conditions
        for($x=0;$x<200;$x++){
            //this loop will determen if the player hasn't had a long pause
            for($z=0;$z<50;$z++){
                //this loop will determen if the persons hasn't been in the same team;
                for($y=0;$y<50;$y++){
                    //determen a random team
                    $random = rand(0, count($teamArray)-1);
                    $teamPlayer1 = isset($teamArray[$random]['player1']) === true ? $teamArray[$random]['player1'] : 9999999;
                    $teamPlayer2 = isset($teamArray[$random]['player2']) === true ? $teamArray[$random]['player2'] : 9999999;

                    if($teamPlayer1 != 9999999 AND $teamPlayer2 != 9999999){
                        if($personArray[$teamPlayer1]['nonPlayedWeeks'] > 2 OR $personArray[$teamPlayer2]['nonPlayedWeeks'] > 2){
                            break;
                        }
                    }
                }
                //if player1 or player2 hasn't played for more then 2 weeks the loop will break and go on to the next controle
                if(isset($personArray[$teamPlayer1]['against'][$teamPlayer2]['name']) === false AND isset($personArray[$teamPlayer2]['against'][$teamPlayer1]['name']) === false){
                    break;
                }
            }
            //check if the meet the required highest games and highest team plays
            if($teamPlayer1 != 9999999 AND $teamPlayer2 != 9999999){
                if($personArray[$teamPlayer1]['totalGames'] < $highestGames AND $personArray[$teamPlayer2]['totalGames'] < $highestGames AND $personArray[$teamPlayer1][$teamNumber] < $highestTeamPlays AND $personArray[$teamPlayer2][$teamNumber] < $highestTeamPlays){
                    break;
                }
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
    protected function removeTeam($userId, $teamArray){
        foreach($teamArray as $key=>$team){
            if($userId == $team['player1'] OR $userId == $team['player2']){
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
    protected function createJsonSeason($gamesArray, $seasonId){
        $arrayJson = array();
        $x=0;
        foreach($gamesArray as $game){
            for($z=1;$z<=3;$z++){
                $team = 'team'.$z;
                $teamplayerOne = isset($game[$team]['player1']) === true ? $game[$team]['player1'] : "";
                $teamplayerTwo = isset($game[$team]['player2']) === true ? $game[$team]['player2'] : "";
                $datum =  $game['datum'];

                $arrayJson['data'][$x]['seasonId'] = $seasonId;
                $arrayJson['data'][$x]['date'] = $datum;
                $arrayJson['data'][$x]['team'] = $team;
                $arrayJson['data'][$x]['user_id1'] = $teamplayerOne;
                $arrayJson['data'][$x]['user_id2'] = $teamplayerTwo;

                $arrayJson['date'][$datum][$team]['player1'] =  $teamplayerOne;
                $arrayJson['date'][$datum][$team]['player2'] =  $teamplayerTwo;

                if($team != "team3"){
                    $arrayJson['stats'][$teamplayerOne]['against'][$teamplayerTwo] = $teamplayerTwo;
                    $arrayJson['stats'][$teamplayerTwo]['against'][$teamplayerOne] = $teamplayerOne;
                }

                isset($arrayJson['stats'][$teamplayerOne][$team]) === true ? $arrayJson['stats'][$teamplayerOne][$team]++ : $arrayJson['stats'][$teamplayerOne][$team] = 1;
                isset($arrayJson['stats'][$teamplayerTwo][$team]) === true ? $arrayJson['stats'][$teamplayerTwo][$team]++ : $arrayJson['stats'][$teamplayerTwo][$team] = 1;
                isset($arrayJson['stats'][$teamplayerOne]['total']) === true ? $arrayJson['stats'][$teamplayerOne]['total']++ : $arrayJson['stats'][$teamplayerOne]['total'] = 1;
                isset($arrayJson['stats'][$teamplayerTwo]['total']) === true ? $arrayJson['stats'][$teamplayerTwo]['total']++ : $arrayJson['stats'][$teamplayerTwo]['total'] = 1;

                $arrayJson['date'][$datum][$teamplayerOne] = $team;
                $arrayJson['date'][$datum][$teamplayerTwo] = $team;
                $arrayJson['date'][$datum]['seasonId'] = $seasonId;
                $arrayJson['date'][$datum]['date'] = $datum;
                $x++;
            }
        }
        return json_encode($arrayJson);
    }
}