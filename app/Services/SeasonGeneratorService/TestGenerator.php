<?php

namespace App\Services\SeasonGeneratorService;

use App\Models\Team;
use App\Models\Season;

/**
    |--------------------------------------------------------------------------
    | Generate SingleFieldOneHourTwoTeams Season
    |--------------------------------------------------------------------------
    | SingleFieldOneHourTwoTeams season: A season is created based on 1 courts for 1(or more) hours and there will be 2 teams,
    |
    | 1 Field: 1(or more) hours double
    |
    | 2 teams wil be created that wil play 1(or more) hour single
    |
    | conditions:
    | - the time between play dates shouldn't be more the 2 weeks(with the exception if players are absence)
    | - Players shouldn't be in the same team with a player twice
    |exception:
    | - if the season is really long then it is possible to play more then once in the same team
    | - If there are alot of absence people on the same day it is possible to play more then once in the same team
    */
class TestGenerator extends AbstractDoubleGenerator implements IGenerator
{
    protected $totalTeams = 2;

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
        $teamShuffle = [1,2];
        shuffle($teamShuffle);
        foreach ($teamShuffle AS $number) {
            $player1 = $player2 = "";
            //teams will be called team + a number
            $teamnumber = "team".$number;
            $date = $seasonDate['date'];

            //prepare the draw error to see which teams will be available for the draw
            $drawTeamArray = $this->removeAbsenceTeams($date, $gamesArray['person'], $gamesArray['allTeams']);
            $drawTeamArray = $this->removeAllPlayedGames($drawTeamArray, $gamesArray['season'], $teamnumber);
             //method to have the active players in the right teams
             if(isset($gamesArray['teamsSetup']) == true){
                $drawTeamArray = $this->removeNonSetTeams($drawTeamArray, $gamesArray['seasonTeams'], $teamnumber, $date);
             }
            
             //prepare the backup array that allows players to be with previous played persons
            $backupTeamArray = $this->removeAbsenceTeams($date, $gamesArray['person'], $gamesArray['allTeams']);
            $backupTeamArray = $this->removeNonSetTeams($backupTeamArray, $gamesArray['seasonTeams'], $teamnumber, $date);

            //choose a random team from the drawArray
            if (count($drawTeamArray) > 0) {
                $random = $this->createRandomTeam($drawTeamArray, $gamesArray['person'], $teamnumber);
                $player1 = isset($drawTeamArray[$random]['player1']) === true ? $drawTeamArray[$random]['player1'] : "";
                $player2 = isset($drawTeamArray[$random]['player2']) === true ? $drawTeamArray[$random]['player2'] : "";
            }

            //choose a random team from the backup
            if (($player1 == "" OR $player2 == "") AND count($backupTeamArray) > 0) {
                $random = $this->createRandomTeam($backupTeamArray, $gamesArray['person'], $teamnumber);
                $player1 = isset($drawTeamArray[$random]['player1']) === true ? $drawTeamArray[$random]['player1'] : "";
                $player2 = isset($drawTeamArray[$random]['player2']) === true ? $drawTeamArray[$random]['player2'] : "";
            }

            //if there are no 2 players then go to next team shuffle
            if ($player1 == "" OR $player2 == "") {
                continue;
            }

             //adds the team id and player id to the game array (needed for saving the data)
             if(isset($gamesArray['teamsSetup']) == true){
                 foreach($gamesArray['teamsSetup'][$date] as $key => $team){
                    $gamesArray['season'][$date][$teamnumber][$player1]['teamId'] = $key;
                    $gamesArray['season'][$date][$teamnumber][$player1]['groupUser'] = $player1;
                    unset($gamesArray['teamsSetup'][$date][$key]);
                    break;
                }
                foreach($gamesArray['teamsSetup'][$date] as $key => $team){
                    $gamesArray['season'][$date][$teamnumber][$player2]['teamId'] = $key;
                    $gamesArray['season'][$date][$teamnumber][$player2]['groupUser'] = $player1;
                    unset($gamesArray['teamsSetup'][$date][$key]);
                    break;
                }
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


            //set the counter to 0 for non played weeks
            $gamesArray['person'][$player1]['nonPlayedWeeks'] = 0;
            $gamesArray['person'][$player2]['nonPlayedWeeks'] = 0;

            //Add team to playday
            $gamesArray['season'][$date][$teamnumber]['player1'] = $player1;
            $gamesArray['season'][$date][$teamnumber]['player2'] = $player2;

            //controle number to see if there are 4 players each week
            if (isset($gamesArray['season'][$date]['gameCounter'])  === false) {
                $gamesArray['season'][$date]['gameCounter'] = 0;
            }
            $gamesArray['season'][$date]['gameCounter'] +=2;
        }
        return $gamesArray;
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
            $personStat[$groupUser->id]['nonPlayedWeeks'] = 0;
        }
        return $personStat;
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
            $team1 = $team2 = 0;
            foreach ($day->user As $users) {
                if($users->team == ""){
                    continue;
                }
                $this->saveTeam($jsonArray->seasonData->id, $day->day, $users->team, $users->groupUser);

                if($users->team == "team1"){
                    $team1++;
                }
                if($users->team == "team2"){
                    $team2++;
                }
            }
            
            if($team1 == 0){
                $this->saveTeam($jsonArray->seasonData->id, $day->day, 'team1');
                $this->saveTeam($jsonArray->seasonData->id, $day->day, 'team1');
            }
            if($team2 == 0){
                $this->saveTeam($jsonArray->seasonData->id, $day->day, 'team2');
                $this->saveTeam($jsonArray->seasonData->id, $day->day, 'team2');
            }
        }
        $this->season->seasonIsGenerated($jsonArray->seasonData->id);
    }
}