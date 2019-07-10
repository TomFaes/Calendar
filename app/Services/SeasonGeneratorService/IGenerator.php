<?php

namespace App\Services\SeasonGeneratorService;

use App\Models\Season;

interface IGenerator {
    public function generateSeason($seasonId);
    public function getPlayDates($beginDate, $endDate);
    public function createJsonSeason($gamesArray, $seasonId);
    public function saveSeason($jsonSeason);
    public function getNextPlayDay(Season $season);
}