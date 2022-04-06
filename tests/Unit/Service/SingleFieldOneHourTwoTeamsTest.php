<?php

namespace Tests\Unit\Service;

use App\Models\Group;
use App\Models\GroupUser as ModelsGroupUser;
use Tests\TestCase;

use App\Models\Season;
use App\Models\User;
use App\Repositories\AbsenceRepo;
use App\Repositories\SeasonRepo;
use App\Repositories\TeamRepo;
use App\Services\SeasonGeneratorService\GeneratorFactory;
use Carbon\Carbon;
use Database\Seeders\GeneratorSeeder;
use DateTime;

class SingleFieldOneHourTwoTeamsTest extends TestCase
{
    protected $repo;
    protected $allGroupUsers;
    protected $getAllGroups;
    protected $getAllGroupUsers;
    protected $newSeason;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->allSeasons = Season::all();
        $this->getAllUsers = User::all();
        $this->getAllGroupUsers = ModelsGroupUser::all();
        $this->getAllGroups = Group::all();

        $this->createSeason();
    }

    /**
     * Build a default season with 5 absence dates. 
     * */ 
    protected function createSeason(){
        //default dataset
        $seasonData = [
            'name' => 'Test Season',
            'begin' => Carbon::now()->format('Y-m-d'),
            'end' =>  Carbon::now()->addMonths(7)->format('Y-m-d'),
            'group_id' =>  $this->getAllGroups[0]->id,
            'admin_id' => $this->getAllUsers[0]->id,
            'start_hour' => "20:00:00",
            'type' => 'SingleFieldOneHourTwoTeams',
            'public' => 0,
            'is_generated' => 0,
            'allow_replacement' => 0, 
        ];
        $seasonRepo = new SeasonRepo();
        $this->newSeason = $seasonRepo->create($seasonData, $this->getAllUsers[0]->id);

        $absenceRepo = new AbsenceRepo();

        for($x = 1; $x <= 20;  $x++){
            $randomDay = rand(1, 30);
            $randomGroupUser = rand(0, 9);
            $data = [
                'season_id' => $this->newSeason->id,
                'date' => Carbon::now()->addDays(7*$randomDay)->format('Y-m-d'),
                'group_user_id' => $this->getAllGroupUsers[$randomGroupUser]->id,
            ];
            $absenceRepo->create($data,  $this->newSeason->id);
        }
    }

    protected function defaultGeneratedSeasonTests($generatedSeason){
        $this->assertArrayHasKey('absenceData', $generatedSeason);
        $this->assertArrayHasKey('currentPlayDay', $generatedSeason);

        $this->assertArrayHasKey('data', $generatedSeason);
        $this->assertArrayHasKey('day', $generatedSeason['data'][0]);
        $this->assertArrayHasKey('user', $generatedSeason['data'][0]);
        $this->assertEquals(count($generatedSeason['data'][0]['user']), 10);

        $this->assertArrayHasKey('groupUserData', $generatedSeason);
        $this->assertEquals(count($generatedSeason['groupUserData']), 10);

        $this->assertArrayHasKey('seasonData', $generatedSeason);

        $groupUser = $this->getAllGroupUsers[0]->id;
        $this->assertArrayHasKey('stats', $generatedSeason);
        $this->assertArrayHasKey('team1', $generatedSeason['stats'][$groupUser]);
        $this->assertArrayHasKey('team2', $generatedSeason['stats'][$groupUser]);
        $this->assertArrayHasKey('total', $generatedSeason['stats'][$groupUser]);
    }

    public function test_generate_season()
    {
        $seasonGenerator = GeneratorFactory::generate($this->newSeason->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->newSeason);

        $this->defaultGeneratedSeasonTests($generatedSeason);
    }

    public function test_get_play_dates()
    {
        $startDate = new DateTime($this->newSeason->begin);
        $endDate = new DateTime($this->newSeason->end);
        $daysInSeason = array();

        while ($startDate <= $endDate) {
            $daysInSeason[]['date'] = $startDate->format("Y-m-d");
            $startDate->add(new \DateInterval('P7D'));
        }

        //generate a season
        $seasonGenerator = GeneratorFactory::generate($this->newSeason->type);
        $playDates = $seasonGenerator->getPlayDates($this->newSeason->begin, $this->newSeason->end);

        $this->assertGreaterThan(0, count($playDates));
        $this->assertEquals(count($playDates), count($daysInSeason));
        for($x=0; $x<count($playDates); $x++){
            $this->assertEquals($playDates[$x], $daysInSeason[$x]);
        }
    }

    public function test_get_save_season_calendar()
    {
        $seasonGenerator = GeneratorFactory::generate($this->newSeason->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->newSeason);
        
        $saveSeason = $seasonGenerator->saveSeason(json_encode($generatedSeason));
        $this->assertNull($saveSeason);
    } 

    public function test_get_season_calendar(){
        $seasonGenerator = GeneratorFactory::generate($this->newSeason->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->newSeason);
        
        $saveSeason = $seasonGenerator->saveSeason(json_encode($generatedSeason));
        $this->assertNull($saveSeason);

        $seasonRepo = new SeasonRepo();
        $season = $seasonRepo->getSeason($this->newSeason->id);
        $seasonCalendar = $seasonGenerator->getSeasonCalendar($season);

        $this->defaultGeneratedSeasonTests($seasonCalendar);
        $this->assertArrayHasKey('teams', $seasonCalendar['data'][0]);
    }

    public function test_generate_empty_season(){
        $seasonGenerator = GeneratorFactory::generate($this->newSeason->type);
        $generatedSeason = $seasonGenerator->generateEmptySeason($this->newSeason);

        $this->assertArrayHasKey('absenceData', $generatedSeason);
        $this->assertArrayHasKey('currentPlayDay', $generatedSeason);

        $this->assertArrayHasKey('data', $generatedSeason);
        $this->assertArrayHasKey('day', $generatedSeason['data'][0]);
        $this->assertArrayHasKey('user', $generatedSeason['data'][0]);
        $this->assertEquals(count($generatedSeason['data'][0]['user']), 10);

        $this->assertArrayHasKey('groupUserData', $generatedSeason);
        $this->assertEquals(count($generatedSeason['groupUserData']), 10);

        $this->assertArrayHasKey('seasonData', $generatedSeason);
        $this->assertArrayHasKey('stats', $generatedSeason);
    }

    public function test_save_prefilled_season(){
        $seasonGenerator = GeneratorFactory::generate($this->newSeason->type);
        $generatedSeason = $seasonGenerator->generateEmptySeason($this->newSeason);

        $generatedSeason = $seasonGenerator->generateSeason($this->newSeason);
        $seasonGenerator->savePrefilledSeason(json_encode($generatedSeason['data']));
        
        $seasonRepo = new SeasonRepo();
        $season = $seasonRepo->getSeason($this->newSeason->id);
        $seasonCalendar = $seasonGenerator->getSeasonCalendar($season);

        $this->defaultGeneratedSeasonTests($seasonCalendar);
    }
}