<?php

namespace App\Services\SeasonGeneratorService;

use App\Models\Season;

interface IGenerator {
    public function generateSeason(Season $season);
    public function getPlayDates($beginDate, $endDate);
    public function getSeasonCalendar(Season $season);
    public function saveSeason($jsonSeason);
    public function generateEmptySeason(Season $season);
    public function savePrefilledSeason($jsonTeam);
}