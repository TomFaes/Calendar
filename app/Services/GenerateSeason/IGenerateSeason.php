<?php

namespace App\Services\GenerateSeason;


interface IGenerateSeason {

    public function createPersonArray(\App\Models\Season $season);
    public function createRandomTeam($teamArray, $gamesArray, $personArray, $teamNumber);
    public function generateSeason($seasonId);
    public function seasonRecap($seasonId);

}