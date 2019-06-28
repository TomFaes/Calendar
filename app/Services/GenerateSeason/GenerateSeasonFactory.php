<?php

namespace App\Services\GenerateSeason;

use Exception;

use App\Repositories\SeasonRepo;
use App\Repositories\AbsenceRepo;
use App\Repositories\TeamRepo;


class GenerateSeasonFactory
{
    public static function generate($type){

        $seasonRepo = new SeasonRepo();
        $absenceRepo = new AbsenceRepo();
        $teamRepo = new TeamRepo();

        // assumes the use of an autoloader
        $generateSeasonName = "App\Services\GenerateSeason\\".$type;

        if (class_exists($generateSeasonName)) {
            return new $generateSeasonName($seasonRepo, $absenceRepo, $teamRepo);
        }
        else {
            return redirect()->to('season/')->with('error', 'er is geen type generator gevonden met volgende naam: '.$type)->send();
        }
    }
}