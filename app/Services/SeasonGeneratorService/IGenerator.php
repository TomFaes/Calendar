<?php

namespace App\Services\SeasonGeneratorService;

use App\Models\Season;

interface IGenerator {
    //public function generateSeason($seasonId);
    public function generateSeason(Season $season);
    public function getPlayDates($beginDate, $endDate);
    public function saveSeason($jsonSeason);
    public function getNextPlayDay(Season $season);
    public function getSeasonCalendar(Season $season);
}