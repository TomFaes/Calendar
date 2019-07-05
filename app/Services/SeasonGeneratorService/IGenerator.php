<?php

namespace App\Services\SeasonGeneratorService;


interface IGenerator {
    public function generateSeason($seasonId);
    public function getPlayDates($beginDate, $endDate);
    public function createJsonSeason($gamesArray, $seasonId);
    public function saveSeason($jsonSeason);



/*
    public function createPersonArray(\App\Models\Season $season);
    public function createRandomTeam($teamArray, $gamesArray, $personArray, $teamNumber);
    public function generateSeason($seasonId);
    public function seasonRecap($seasonId);
*/
}