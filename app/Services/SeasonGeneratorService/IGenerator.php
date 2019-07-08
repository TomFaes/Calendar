<?php

namespace App\Services\SeasonGeneratorService;

interface IGenerator {
    public function generateSeason($seasonId);
    public function getPlayDates($beginDate, $endDate);
    public function createJsonSeason($gamesArray, $seasonId);
    public function saveSeason($jsonSeason);
    public function getNextPlayDay($seasonId, $day, $hour);
}