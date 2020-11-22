<?php

namespace Tests\Unit\Repository;

use Carbon\Carbon;

use Tests\TestCase;
use App\Models\Team;
use App\Models\Season;
use App\Models\GroupUser;

use App\Repositories\TeamRepo;

class TeamTest extends TestCase
{
    protected $testData;
    protected $data;
    protected $repo;
    protected $allGroupUsers;
    protected $getAllSeasons;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();

        $this->repo =  new TeamRepo();
        $this->testData = Team::with(['group_user', 'season'])->get();

        $this->getAllGroupUsers = GroupUser::all();
        $this->getAllSeasons = Season::all();

        //default dataset
        $this->data = [
            'season_id' => $this->getAllSeasons[0]->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'team' => 'team1',
            'group_user_id' => $this->getAllGroupUsers[0]->id,
        ];
    }

    /**
     * Default data test
     */
    protected function dataTests($data, $testData) : void
    {
        $this->assertInstanceOf(Team::class, $testData);
        $this->assertEquals($data['season_id'], $testData->season_id);
        $this->assertEquals($data['date'], $testData->date);
        $this->assertEquals($data['group_user_id'], $testData->group_user_id);
    }

    public function test_get_all_teams()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m Team Repository Test:   [0m';
        
        $found = $this->repo->getAllTeams();
        $this->assertEquals(10, count($found));
        echo PHP_EOL.'[42m OK  [0m get all teams';
    }

    public function test_get_team()
    {        
        $found = $this->repo->getTeam($this->testData[0]->id);
        $this->dataTests($this->testData[0], $found);
        echo PHP_EOL.'[42m OK  [0m get team';
    }

    public function test_get_all_teams_of_a_season()
    {
        $found = $this->repo->getSeasonUsers($this->getAllSeasons[1]->id);
        $this->assertIsArray($found);
        echo PHP_EOL.'[42m OK  [0m get all teams of a season';
    }

    public function test_delete_absence()
    {
        $testSeasonId = $this->testData[0]->season_id;
        $found = $this->repo->deleteTeam($this->testData[0]->id);
        $this->assertEquals($testSeasonId, $found);

        $found = $this->repo->getAllTeams();
        $this->assertEquals(9, count($found));
       
        echo PHP_EOL.'[42m OK  [0m delete team';
    }

    //this method doesn't have a return
    public function test_save_team()
    {
        $this->data = [
            'season_id' => $this->getAllSeasons[0]->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'team' => 'team1',
            'group_user_id' => $this->getAllGroupUsers[0]->id,
        ];

        $team = new Team();
        $team->season_id = $this->data['season_id'];
        $team->date = $this->data['date'];
        $team->team = $this->data['team'];
        $team->group_user_id = $this->data['group_user_id'];
        $found = $this->repo->saveTeam($team);

        $this->dataTests($this->data, $found);
        echo PHP_EOL.'[42m OK  [0m create user';
    }



    



/*
    public function saveTeam(Team $team);
*/
}