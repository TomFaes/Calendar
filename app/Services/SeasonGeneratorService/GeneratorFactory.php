<?php

namespace App\Services\SeasonGeneratorService;

use Exception;

use App\Services\AbsenceService;

class GeneratorFactory
{
    public static function generate($type)
    {
        $absenceService = new AbsenceService();

        // assumes the use of an autoloader
        $generateSeasonName = "App\Services\SeasonGeneratorService\\".$type;

        if (class_exists($generateSeasonName)) {
            return new $generateSeasonName($absenceService);
        } else {
            return redirect()->to('season/')->with('error', 'er is geen type generator gevonden met volgende naam: '.$type)->send();
        }
    }
}