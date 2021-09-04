<?php

namespace Tests\Unit\Service;

use Tests\TestCase;

use App\Models\User;
use App\Models\Season;
use App\Services\SeasonGeneratorService\GeneratorFactory;


class TwoFieldTwoHourThreeTeamsTest extends TestCase
{
    
    protected $repo;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();

        $this->allSeasons = Season::all();
     
        //default dataset
        $this->data = [
            'firstname' => 'Firstname',
        ];
    }

    public function test_generateSeason()
    {
        echo "\n\n--------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m Two Field Two Hour Three Teams Test:   [0m';

        //generate a season
        $seasonGenerator = GeneratorFactory::generate($this->allSeasons[0]->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->allSeasons[0]);

        $this->assertArrayHasKey('seasonData', $generatedSeason);
        $this->assertArrayHasKey('absenceData', $generatedSeason);
        $this->assertArrayHasKey('groupUserData', $generatedSeason);
        $this->assertArrayHasKey('data', $generatedSeason);      
        
        echo PHP_EOL.'[42m OK  [0m test generate season';
    }

    public function test_getPlayDates()
    {
        $startDate = new \DateTime($this->allSeasons[0]->begin);
        $endDate = new \DateTime($this->allSeasons[0]->end);
        $daysInSeason = array();

        while ($startDate <= $endDate) {
            $daysInSeason[]['date'] = $startDate->format("Y-m-d");
            $startDate->add(new \DateInterval('P7D'));
        }

        //generate a season
        $seasonGenerator = GeneratorFactory::generate($this->allSeasons[0]->type);
        $playDates = $seasonGenerator->getPlayDates($this->allSeasons[0]->begin, $this->allSeasons[0]->end);

        $this->assertEquals(count($playDates), count($daysInSeason));
        for($x=0; $x<count($playDates); $x++){
            $this->assertEquals($playDates[$x], $daysInSeason[$x]);
        }

        echo PHP_EOL.'[42m OK  [0m test get play dates';
    }

    public function test_getSeasonCalendar()
    {
        //generate a season
        $seasonGenerator = GeneratorFactory::generate($this->allSeasons[0]->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->allSeasons[0]);
        $jsonSeason = json_encode($generatedSeason);
        $saveSeason = $seasonGenerator->saveSeason($jsonSeason);

        $this->assertNull($saveSeason);
        echo PHP_EOL.'[42m OK  [0m test get calendar season';
    }    
}