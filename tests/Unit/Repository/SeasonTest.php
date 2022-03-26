<?php

namespace Tests\Unit\Repository;

use Carbon\Carbon;

use Tests\TestCase;
use App\Models\Season;
use App\Models\Group;
use App\Models\User;
use App\Repositories\GroupUserRepo;
use App\Repositories\SeasonRepo;
use App\Services\SeasonGeneratorService\GeneratorFactory;
use Database\Seeders\GeneratorSeeder;

class SeasonTest extends TestCase
{
    protected $testData;
    protected $defaultSeason;
    protected $repo;

    protected $allUsers;
    protected $allGroups;

    protected $recordCount;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->repo =  new SeasonRepo();
        $this->testData = Season::with(['group', 'admin'])->get();
        $this->recordCount = count($this->testData);

        $this->getAllUsers = User::all();
        $this->getAllGroups = Group::all();

        /*
        $groupUser = new GroupUserRepo();
        $this->getAllGroupUsers = $groupUser->getAllGroupUsers();
        //connect a group user to a user
        $data['user_id'] = $this->getAllUsers[0]->id;
        $groupUser->update($data,  $this->getAllGroupUsers[0]->id);
        */

        //default dataset
        $this->defaultSeason = [
            'name' => 'a season this year',
            'begin' => Carbon::now()->format('Y-m-d'),
            'end' =>  Carbon::now()->addMonths(7)->format('Y-m-d'),
            'group_id' =>  $this->getAllGroups[0]->id,
            'admin_id' => $this->getAllUsers[0]->id,
            'start_hour' => "20:00:00",
            'type' => 'TwoFieldTwoHourThreeTeams',
            'public' => 1,
            'is_generated' => 0,
            'allow_replacement' => 1, 
        ];
    }

    /**
     * Default data test
     */
    protected function dataTests($data, $testData) : void
    {
        $this->assertInstanceOf(Season::class, $testData);
        $this->assertEquals($data['name'], $testData->name);
        $this->assertEquals($data['begin'], $testData->begin);
        $this->assertEquals($data['end'], $testData->end);
        $this->assertEquals($data['group_id'], $testData->group_id);
        $this->assertEquals($data['admin_id'], $testData->admin_id);
        $this->assertEquals($data['start_hour'], $testData->start_hour);
        $this->assertEquals($data['type'], $testData->type);
        $this->assertEquals($data['public'], $testData->public);
        $this->assertEquals($data['is_generated'], $testData->is_generated);
        $this->assertEquals($data['allow_replacement'], $testData->allow_replacement);
    }

    public function test_get_all_seasons()
    {       
        $found = $this->repo->getAllSeasons();
        $this->assertEquals($this->recordCount, count($found));
    }

    public function test_get_season()
    {        
        $found = $this->repo->getSeason($this->testData[0]->id);
        $this->dataTests($this->testData[0], $found);
    }

    public function test_get_active_seasons_of_user()
    {
        //attach user to a group user
        $data['user_id'] = $this->getAllUsers[0]->id;
        $groupUserRepo = new GroupUserRepo();
        $groupUserRepo->update($data, $this->getAllGroups[0]->groupUsers[0]->id);

        //create an old season and generate it
        $this->defaultSeason['begin'] = Carbon::now()->subDays(600)->format('Y-m-d');
        $this->defaultSeason['end'] = Carbon::now()->subDays(500)->format('Y-m-d');
        $season = $this->repo->create($this->defaultSeason, $this->getAllUsers[0]->id);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $generatedSeason = $seasonGenerator->generateSeason($season);
        $seasonGenerator->saveSeason(json_encode($generatedSeason));

        //generate all teh other seasons
        foreach($this->testData AS $test){
            $seasonGenerator = GeneratorFactory::generate($test->type);
            $generatedSeason = $seasonGenerator->generateSeason($test);
            $seasonGenerator->saveSeason(json_encode($generatedSeason));
        }

        $found = $this->repo->getActiveSeasons($this->testData[0]->admin_id);
        $this->assertEquals($this->recordCount, count($found));
    }

    public function test_check_if_season_is_started()
    {
        //season is started
        $this->defaultSeason['name'] = "check if season is started";
        $this->defaultSeason['begin'] = Carbon::now()->subDays(5)->format('Y-m-d');
        $season = $this->repo->create($this->defaultSeason, $this->getAllUsers[0]->id);
        $found = $this->repo->checkIfSeasonIsStarted($season->id);
        $this->assertEquals(count($found), 0);

        //season is not started
        $this->defaultSeason['name'] = "check if season is started not started";
        $this->defaultSeason['begin'] = Carbon::now()->addDays(5)->format('Y-m-d');
        $season = $this->repo->create($this->defaultSeason, $this->getAllUsers[0]->id);
        $found = $this->repo->checkIfSeasonIsStarted($season->id);

        $this->dataTests($this->defaultSeason, $found[0]);
    }

    public function test_get_seasons_from_group()
    {
        $tested_group = $this->testData[0]->group_id;
        $found = $this->repo->getGroupOfSeason($tested_group);
        $this->assertEquals($this->recordCount, count($found));

        //create a season with another group
        $this->defaultSeason['group_id'] = $this->getAllGroups[1]->id;
        $found = $this->repo->getGroupOfSeason($tested_group);
        $this->assertEquals($this->recordCount, count($found));
    }

    public function test_get_seasons_of_a_user()
    {
        $tested_user = $this->testData[0]->admin_id;
        $found = $this->repo->getSeasonsOfUser($tested_user);
        $this->assertEquals($this->recordCount, count($found));
    }

    public function test_create_season()
    {
        $season = $this->repo->create($this->defaultSeason, $this->getAllUsers[0]->id);
        $this->dataTests($this->defaultSeason, $season);
    }

    public function test_update_season_with_season_draw_zero()
    {
        $season = $this->repo->update($this->defaultSeason, $this->testData[1]->id);
        $this->dataTests($this->defaultSeason, $season);
    }

    public function test_update_season_with_season_draw_bigger_then_zero()
    {
        $season = $this->repo->update($this->defaultSeason, $this->testData[1]->id);

        $this->assertInstanceOf(Season::class, $season);
        $this->assertEquals($this->defaultSeason['name'], $season->name);
        $this->assertEquals($this->defaultSeason['start_hour'], $season->start_hour);
        $this->assertEquals($this->defaultSeason['public'], $season->public);
        $this->assertEquals($this->defaultSeason['allow_replacement'], $season->allow_replacement);
    }

    public function test_is_generated_season()
    {
        $season = $this->repo->seasonIsGenerated($this->testData[0]->id);
        $this->assertEquals(1, $season->is_generated);
    }

    public function test_delete_season()
    {
        $this->repo->delete($this->testData[0]->id);
        $found = $this->repo->getAllSeasons();
        $this->assertEquals(($this->recordCount - 1), count($found));
    }
}