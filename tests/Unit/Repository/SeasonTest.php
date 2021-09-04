<?php

namespace Tests\Unit\Repository;

use Carbon\Carbon;

use Tests\TestCase;
use App\Models\Season;
use App\Models\Group;
use App\Models\User;

use App\Repositories\SeasonRepo;
use App\Repositories\GroupUserRepo;
use App\Services\SeasonGeneratorService\GeneratorFactory;

class SeasonTest extends TestCase
{
    protected $testData;
    protected $data;
    protected $repo;
    protected $allUsers;
    protected $GroupUserRepo;
    protected $allGroups;
    protected $recordCount;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();

        $this->repo =  new SeasonRepo();
        $this->testData = Season::with(['group', 'admin'])->get();
        $this->recordCount = count($this->testData);

        $this->getAllUsers = User::all();
        $this->getAllGroups = Group::all();

        $groupUser = new GroupUserRepo();
        $this->getAllGroupUsers = $groupUser->getAllGroupUsers();
        //connect a group user to a user
        $data['user_id'] = $this->getAllUsers[0]->id;
        $groupUser->update($data,  $this->getAllGroupUsers[0]->id);

        //default dataset
        $this->data = [
            'name' => 'a season this year',
            'begin' => Carbon::now()->format('Y-m-d'),
            'end' =>  Carbon::now()->addMonths(7)->format('Y-m-d'),
            'group_id' =>  $this->getAllGroups[0]->id,
            'admin_id' => $this->getAllUsers[0]->id,
            'start_hour' => "20:00:00",
            'type' => 'TwoFieldTwoHourThreeTeams',
            'public' => 0,
            'allow_replacement' => 0, 
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
        $this->assertEquals($data['allow_replacement'], $testData->allow_replacement);
    }

    public function test_get_all_seasons()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m Season Repository Test:   [0m';
        
        $found = $this->repo->getAllSeasons();
        $this->assertEquals($this->recordCount, count($found));
        echo PHP_EOL.'[42m OK  [0m get all seasons';
    }

    public function test_get_season()
    {        
        $found = $this->repo->getSeason($this->testData[0]->id);
        $this->dataTests($this->testData[0], $found);
        echo PHP_EOL.'[42m OK  [0m get season';
    }

    public function test_get_active_seasons_of_user()
    {
        $this->assertEquals(1, 1);
        echo PHP_EOL.'[41m Rewrite  [0m get active seasons';
        /*
        //generate a season
        $seasonGenerator = GeneratorFactory::generate($this->testData[0]->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->testData[0]);
        $jsonSeason = json_encode($generatedSeason);
        $seasonGenerator->saveSeason($jsonSeason);

        $found = $this->repo->getActiveSeasons($this->getAllUsers[0]->id);
        $this->assertEquals(1, count($found));

        //generate a 2nd season
        $seasonGenerator = GeneratorFactory::generate($this->testData[1]->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->testData[1]);
        $jsonSeason = json_encode($generatedSeason);
        $seasonGenerator->saveSeason($jsonSeason);

        $found = $this->repo->getActiveSeasons($this->getAllUsers[0]->id);
        $this->assertEquals(2, count($found));
        
        echo PHP_EOL.'[42m OK  [0m get active seasons';
        */
    }

    public function test_get_seasons_from_group()
    {
        $this->assertEquals(1, 1);
        echo PHP_EOL.'[41m Rewrite  [0m get seasons from a group';

        //$found = $this->repo->getGroupOfSeason($this->getAllGroups[0]->id);
        //$this->assertEquals(10, count($found));
        //echo PHP_EOL.'[42m OK  [0m get seasons from a group';
    }

    public function test_get_seasons_of_a_user()
    {
        $this->assertEquals(1, 1);
        echo PHP_EOL.'[41m Rewrite  [0m get seasons from a user';

        //$found = $this->repo->getSeasonsOfUser($this->getAllUsers[0]->id);
        //$this->assertEquals(10, count($found));
        //echo PHP_EOL.'[42m OK  [0m get seasons from a user';
    }

    public function test_create_season()
    {
        $season = $this->repo->create($this->data, $this->getAllUsers[0]->id);        
        $this->dataTests($this->data, $season);
        echo PHP_EOL.'[42m OK  [0m create a season';
    }

    public function test_update_season()
    {
        $data = [
            'name' => 'an updated season',
            //'begin' => Carbon::now()->format('Y-m-d'),
            //'end' =>  Carbon::now()->addMonths(9)->format('Y-m-d'),
            'begin' => $this->testData[0]->begin,
            'end' =>  $this->testData[0]->end,
            'group_id' =>  $this->testData[0]->group_id,
            'admin_id' => $this->testData[0]->admin_id,
            'start_hour' => "21:00:00",
            'type' => 'TwoFieldTwoHourThreeTeams',
            'public' => 1,
            'allow_replacement' => 1
        ];
        $season = $this->repo->update($data, $this->testData[0]->id);
        $this->dataTests($data, $season);
        echo PHP_EOL.'[41m Rewrite  [0m update season';
    }

    public function test_is_generated_season()
    {
        
        $season = $this->repo->seasonIsGenerated($this->testData[0]->id);
        $this->assertEquals(1, $season->is_generated);
        
        echo PHP_EOL.'[42m OK  [0m is generated season';
    }

    public function test_delete_season()
    {
        $this->repo->delete($this->testData[0]->id);
        $found = $this->repo->getAllSeasons();
        $this->assertEquals(($this->recordCount - 1), count($found));
        echo PHP_EOL.'[42m OK  [0m delete season';
    }
}