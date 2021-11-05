<?php

namespace Tests\Unit\Repository;

use App\Models\Group;
use Carbon\Carbon;

use Tests\TestCase;
use App\Models\Team;
use App\Models\GroupUser;
use App\Models\User;
use App\Repositories\AbsenceRepo;
use App\Repositories\SeasonRepo;
use App\Repositories\TeamRepo;
use App\Services\SeasonGeneratorService\GeneratorFactory;
use Database\Seeders\GeneratorSeeder;

class TeamTest extends TestCase
{
    protected $repo;

    protected $getAllUsers;
    protected $allGroupUsers;
    protected $getAllGroups;
    protected $getAllGroupUsers;
    protected $newSeason;

    protected $generatedSeason;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->repo =  new TeamRepo();
        
        $this->getAllUsers = User::all();
        $this->getAllGroupUsers = GroupUser::all();
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
            'type' => 'TwoFieldTwoHourThreeTeams',
            'public' => 0,
            'is_generated' => 0,
            'allow_replacement' => 0, 
        ];
        $seasonRepo = new SeasonRepo();
        $this->newSeason = $seasonRepo->create($seasonData, $this->getAllUsers[0]->id);

        $absenceRepo = new AbsenceRepo();

        for($x = 1; $x <= 5;  $x++){
            $data = [
                'season_id' => $this->newSeason->id,
                'date' => Carbon::now()->addDays(7*$x)->format('Y-m-d'),
                'group_user_id' => $this->getAllGroupUsers[0]->id,
            ];
            $absenceRepo->create($data,  $this->newSeason->id);
        }

        $seasonGenerator = GeneratorFactory::generate($this->newSeason->type);
        $this->generatedSeason = $seasonGenerator->generateSeason($this->newSeason);
        $this->generatedSeason = $seasonGenerator->saveSeason(json_encode($this->generatedSeason));

        $this->recordCount = count(Team::where('season_id', $this->newSeason->id)->get());
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

    public function test_get_team()
    {
        $allTeams = $this->repo->getAllSeasonTeams($this->newSeason->id);
        $found = $this->repo->getTeam($allTeams[0]->id);

        $this->assertInstanceOf(Team::class, $found);
    }

    public function test_get_all_teams_of_a_season()
    {
        $found = $this->repo->getAllSeasonTeams($this->newSeason->id);

        $this->assertEquals(count($found), $this->recordCount);
        $this->assertGreaterThan(0, count($found));
    }

    public function test_get_teams_on_a_certain_date(){
        $found = $this->repo->getTeamsOnDate($this->newSeason->id, Carbon::now()->addDays(7)->format('Y-m-d'));
        
        if($this->newSeason->type == "TwoFieldTwoHourThreeTeams"){
            $this->assertEquals(count($found), 6);
        }else{
            $this->markTestIncomplete(
                "This test hasn't been created for ".$this->newSeason->type
              );
        }
    }

    public function test_get_filled_dates_in_a_season(){
        $found = $this->repo->getFilledDatesInSeason($this->newSeason->id);
        $this->assertEquals(count($found), $this->recordCount);
        $this->assertGreaterThan(0, count($found));
    }

    public function test_delete_team()
    {
        $allTeams = $this->repo->getAllSeasonTeams($this->newSeason->id);

        $found = $this->repo->deleteTeam($allTeams[0]->id);
        $this->assertEquals($this->newSeason->id, $found);

        $found = $this->repo->getAllSeasonTeams($this->newSeason->id);
        $this->assertEquals(($this->recordCount - 1), count($found));

        $this->repo->deleteTeam($allTeams[1]->id);
        $this->repo->deleteTeam($allTeams[2]->id);
        $this->repo->deleteTeam($allTeams[3]->id);
        $this->repo->deleteTeam($allTeams[4]->id);
        $found = $this->repo->getAllSeasonTeams($this->newSeason->id);
        $this->assertEquals(($this->recordCount - 5), count($found));
    }

    public function test_delete_all_teams_from_a_season(){
        $found = $this->repo->deleteTeamsFromSeason($this->newSeason->id);
        $found = $this->repo->getAllSeasonTeams($this->newSeason->id);
        $this->assertEquals(count($found), 0);
    }

    public function test_save_team(){
        $allTeams = $this->repo->getAllSeasonTeams($this->newSeason->id);

        $found = $this->repo->getTeam($allTeams[0]->id);
        $found->group_user_id = null;
        $found = $this->repo->saveTeam($found);
        $this->assertEquals($found->group_user_id, null);

        $found = $this->repo->getFilledDatesInSeason($this->newSeason->id);
        $this->assertEquals(($this->recordCount - 1), count($found));
    }


    public function test_ask_for_replacement()
    {
        $allTeams = $this->repo->getAllSeasonTeams($this->newSeason->id);
        $found = $this->repo->getTeam($allTeams[0]->id);
        $response = $this->repo->askForReplacement($found);
        $found = $this->repo->getTeam($allTeams[0]->id);

        $this->assertEquals($found->ask_for_replacement, 1);
        $this->assertEquals($response->ask_for_replacement, 1);
        $this->assertEquals($found->ask_for_replacement, $response->ask_for_replacement);
    }

    public function test_cancel_request_for_replacement()
    {
        //ask for replacement
        $allTeams = $this->repo->getAllSeasonTeams($this->newSeason->id);
        $found = $this->repo->getTeam($allTeams[0]->id);
        $response = $this->repo->askForReplacement($found);
        $found = $this->repo->getTeam($allTeams[0]->id);

        $this->assertEquals($found->ask_for_replacement, 1);
        $this->assertEquals($response->ask_for_replacement, 1);
        $this->assertEquals($found->ask_for_replacement, $response->ask_for_replacement);

        //cancel for replacement
        $found = $this->repo->getTeam($allTeams[0]->id);
        $response = $this->repo->cancelRequestForReplacement($found);
        $found = $this->repo->getTeam($allTeams[0]->id);
        $this->assertEquals($found->ask_for_replacement, 0);
        $this->assertEquals($response->ask_for_replacement, 0);
        $this->assertEquals($found->ask_for_replacement, $response->ask_for_replacement);
    }

    public function test_confirm_replacement()
    {
        //ask for replacement
        $allTeams = $this->repo->getAllSeasonTeams($this->newSeason->id);
        $found = $this->repo->getTeam($allTeams[0]->id);
        $response = $this->repo->askForReplacement($found);
        $found = $this->repo->getTeam($allTeams[0]->id);

        $this->assertEquals($found->ask_for_replacement, 1);
        $this->assertEquals($response->ask_for_replacement, 1);
        $this->assertEquals($found->ask_for_replacement, $response->ask_for_replacement);

        //confirm replacement
        $response = $this->repo->confirmReplacement($found, $this->getAllGroupUsers[1]->id);
        $found = $this->repo->getTeam($allTeams[0]->id);

        $this->assertEquals($found->ask_for_replacement, 0);
        $this->assertEquals($response->ask_for_replacement, 0);
        $this->assertEquals($found->ask_for_replacement, $response->ask_for_replacement);
        $this->assertEquals($found->group_user_id, $this->getAllGroupUsers[1]->id);
    }
    
}