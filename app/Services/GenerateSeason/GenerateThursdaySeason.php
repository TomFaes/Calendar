<?php

namespace App\Services\GenerateSeason;

use App\Models\Season;
use App\Models\Team;
use Illuminate\Http\Request;

/*
    |--------------------------------------------------------------------------
    | Generate Thursday Season
    |--------------------------------------------------------------------------
    | Thursday season: A season is created based on 2 courts for 2 hours and there will be 3 teams,
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
class GenerateThursdaySeason extends GenerateSeason implements IGenerateSeason{
    /**
     * set the user Array with some default data
     * @param Season $season
     * @return array
     */
    public function createPersonArray(Season $season){
        $personArray = array();
        foreach($season->group->users as $user){
            $personArray[$user->id]['id'] = $user->id;
            $personArray[$user->id]['name'] = $user->firstname." ".$user->name;
            $personArray[$user->id]['totalGames'] = 0;
            $personArray[$user->id]['datumAbsent'] = $this->getUserAbsenceDays($user->id, $season->id);
            $personArray[$user->id]['team1'] = 0;
            $personArray[$user->id]['team2'] = 0;
            $personArray[$user->id]['team3'] = 0;
            $personArray[$user->id]['nonPlayedWeeks'] = 0;
        }
        return $personArray;
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
     * Remove all played games from the gameArray
     * @param $drawTeamArray
     * @param $gamesArray
     * @param $teamnumber
     * @return array
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
     * @param $gamesArray
     * @param $personArray
     * @param $teamNumber
     * @return int
     */
    public function createRandomTeam($teamArray, $gamesArray, $personArray, $teamNumber){
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
                    $teamPlayer1 = isset($teamArray[$random]['player1']) === true ? $teamArray[$random]['player1'] : 9999;
                    $teamPlayer2 = isset($teamArray[$random]['player2']) === true ? $teamArray[$random]['player2'] : 9999;

                    if($teamPlayer1 != 9999 AND $teamPlayer2 != 9999){
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
            if($teamPlayer1 != 9999 AND $teamPlayer2 != 9999){
                if($personArray[$teamPlayer1]['totalGames'] < $highestGames AND $personArray[$teamPlayer2]['totalGames'] < $highestGames AND $personArray[$teamPlayer1][$teamNumber] < $highestTeamPlays AND $personArray[$teamPlayer2][$teamNumber] < $highestTeamPlays){
                    break;
                }
            }
        }
        return $random;
    }

    public function generateSeason($seasonId){
        //get the Season object
        $season = $this->season->getSeason($seasonId);

        //create all possible teams
        $allPossibleTeamArray = $this->createAllPossibleTeams($season->group);

        //create the personArray to store data for the season
        $personArray = $this->createPersonArray($season);

        //get all date for the season
        $seasonDates = $this->season->get7DaySeasonDates($seasonId);
        //shuffle($seasonDates);
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
                    $random = $this->createRandomTeam($drawTeamArray, $gamesArray, $personArray, $teamnumber);
                }
                //set the player 1 and 2 id
                $player1 = isset($drawTeamArray[$random]['player1']) === true ? $drawTeamArray[$random]['player1'] : "";
                $player2 = isset($drawTeamArray[$random]['player2']) === true ? $drawTeamArray[$random]['player2'] : "";

                //check if there is a second player, if not randomly create a team from the backup
                if($player2 == ""){
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
//check if the players have played against each other

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
     * creates a preview to the season, This array will created in the same way as the seasonview from the calendar
     * @param $seasonId
     * @return array
     */
    public function seasonRecap($seasonId){
        $season = $this->season->getSeason($seasonId);
        $seasonArray = array();
        $teamNumber = $teamplayerOne = $teamplayerTwo = "";
        foreach($season->team as $team){
            if($teamNumber == $team->team){
                $teamplayerTwo = $team->player_id;
                $seasonArray['datum'][$team->date][$team->team]['player1'] = $teamplayerOne;
                $seasonArray['datum'][$team->date][$team->team]['player2'] = $teamplayerTwo;
                if($team->team != "team3") {
                    $seasonArray['stats'][$teamplayerOne]['against'][$teamplayerTwo] = $teamplayerTwo;
                    $seasonArray['stats'][$teamplayerTwo]['against'][$teamplayerOne] = $teamplayerOne;
                }
                
                isset($seasonArray['stats'][$teamplayerOne][$teamNumber]) === true ? $seasonArray['stats'][$teamplayerOne][$teamNumber]++ : $seasonArray['stats'][$teamplayerOne][$teamNumber] = 1;
                isset($seasonArray['stats'][$teamplayerTwo][$teamNumber]) === true ? $seasonArray['stats'][$teamplayerTwo][$teamNumber]++ : $seasonArray['stats'][$teamplayerTwo][$teamNumber] = 1;
                isset($seasonArray['stats'][$teamplayerOne]['total']) === true ? $seasonArray['stats'][$teamplayerOne]['total']++ : $seasonArray['stats'][$teamplayerOne]['total'] = 1;
                isset($seasonArray['stats'][$teamplayerTwo]['total']) === true ? $seasonArray['stats'][$teamplayerTwo]['total']++ : $seasonArray['stats'][$teamplayerTwo]['total'] = 1;
            }
            $seasonArray['datum'][$team->date][$team->player_id] = $team->team;
            $seasonArray['datum'][$team->date]['seasonId'] = $season->id;
            $seasonArray['datum'][$team->date]['date'] = $team->date;
            
            $teamplayerOne = $team->player_id;
            $teamNumber = $team->team;
        }
        return $seasonArray;
    }
    
    /**
     * this function will create a json of the season. This will be sent to the screen so when the season is ok it can be saved in an easy way.
     * The json should always have the same structure so it can be saved in the same way if there are more generators
     * @param $gamesArray
     * @param $seasonId
     * @return string
     */
    public function createJsonSeason($gamesArray, $seasonId, $numberOfTeams){
        $arrayJson = array();
        $x=0;
        foreach($gamesArray as $game){
            for($z=1;$z<=$numberOfTeams;$z++){
                $team = 'team'.$z;
                $arrayJson[$x]['seasonId'] = $seasonId;
                $arrayJson[$x]['date'] = $game['datum'];
                $arrayJson[$x]['team'] = $team;
                $arrayJson[$x]['user_id1'] = isset($game[$team]['player1']) === true ? $game[$team]['player1'] : "";
                $arrayJson[$x]['user_id2'] = isset($game[$team]['player2']) === true ? $game[$team]['player2'] : "";
                $x++;
            }
        }
        return json_encode($arrayJson);
    }

    /**
     * this function will be used to create a view before the season is saved. The extra stats will give the user the option to save it or reload it if it doesn't meet te requirements
     * The Array should always have the same structure so it can be saved in the same way if there are more generators
     * @param $jsonSeason
     * @return array
     */
    public function convertJsonToArraySeason($jsonSeason){
        $jsonData = json_decode($jsonSeason);
        $seasonArray = array();
        foreach($jsonData as $data){
            $teamplayerOne = $data->user_id1;
            $teamplayerTwo = $data->user_id2;
            $teamNumber = substr($data->team, -1);
            $teamNumber = $data->team;
            
            $seasonArray['datum'][$data->date][$data->team]['player1'] = $teamplayerOne;
            $seasonArray['datum'][$data->date][$data->team]['player2'] = $teamplayerTwo;

            if($data->team != "team3"){
                $seasonArray['stats'][$teamplayerOne]['against'][$teamplayerTwo] = $teamplayerTwo;
                $seasonArray['stats'][$teamplayerTwo]['against'][$teamplayerOne] = $teamplayerOne;
            }

            isset($seasonArray['stats'][$teamplayerOne][$teamNumber]) === true ? $seasonArray['stats'][$teamplayerOne][$teamNumber]++ : $seasonArray['stats'][$teamplayerOne][$teamNumber] = 1;
            isset($seasonArray['stats'][$teamplayerTwo][$teamNumber]) === true ? $seasonArray['stats'][$teamplayerTwo][$teamNumber]++ : $seasonArray['stats'][$teamplayerTwo][$teamNumber] = 1;
            isset($seasonArray['stats'][$teamplayerOne]['total']) === true ? $seasonArray['stats'][$teamplayerOne]['total']++ : $seasonArray['stats'][$teamplayerOne]['total'] = 1;
            isset($seasonArray['stats'][$teamplayerTwo]['total']) === true ? $seasonArray['stats'][$teamplayerTwo]['total']++ : $seasonArray['stats'][$teamplayerTwo]['total'] = 1;
            
            $seasonArray['datum'][$data->date][$teamplayerOne] = $data->team;
            $seasonArray['datum'][$data->date][$teamplayerTwo] = $data->team;
            $seasonArray['datum'][$data->date]['seasonId'] = $data->seasonId;
            $seasonArray['datum'][$data->date]['date'] = $data->date;
            
        }
        return $seasonArray;
    }

    /**
     * Saves all the teams from the json file to the database
     * @param $jsonSeason
     */
    public function saveSeason($jsonSeason){
        $jsonArray = json_decode($jsonSeason);
        foreach($jsonArray As $teams){
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
}