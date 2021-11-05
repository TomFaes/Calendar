<?php

namespace Tests\Unit\Repository;

use Carbon\Carbon;

use Tests\TestCase;
use App\Models\Absence;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;

use App\Repositories\AbsenceRepo;
use App\Repositories\SeasonRepo;

use Database\Seeders\GeneratorSeeder;

class AbsenceTest extends TestCase
{
    protected $testData;
    protected $repo;
    protected $seasonRepo;

    protected $allUsers;
    protected $getAllGroupUsers;
    protected $getAllGroups;
    protected $newSeason;

    protected $recordCount;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->repo =  new AbsenceRepo();
        $this->testData = Absence::with(['user', 'season'])->get();
        $this->recordCount = count($this->testData);

        $this->getAllUsers = User::all();
        $this->getAllGroups = Group::all();
        $this->getAllGroupUsers = GroupUser::all();
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
        $this->seasonRepo = new SeasonRepo();
        $this->newSeason = $this->seasonRepo->create($seasonData, $this->getAllUsers[0]->id);

        for($x = 1; $x <= 5;  $x++){
            $data = [
                'season_id' => $this->newSeason->id,
                'date' => Carbon::now()->addDays(7*$x)->format('Y-m-d'),
                'group_user_id' => $this->getAllGroupUsers[0]->id,
            ];
            $this->repo->create($data,  $this->newSeason->id);
        }
    }

    /**
     * Default data test
     */
    protected function dataTests($data, $testData) : void
    {
        $this->assertInstanceOf(Absence::class, $testData);
        $this->assertEquals($data['season_id'], $testData->season_id);
        $this->assertEquals($data['date'], $testData->date);
        $this->assertEquals($data['group_user_id'], $testData->group_user_id);
    }

    public function test_get_all_absences()
    {        
        $found = $this->repo->getAllAbsences();
        $this->assertEquals($this->recordCount, count($found));
    }

    public function test_get_absence()
    {        
        $found = $this->repo->getAbsence($this->testData[0]->id);
        $this->dataTests($this->testData[0], $found);
    }

    public function test_get_absence_of_a_user_in_a_season()
    {
        $this->createSeason();
        $found = $this->repo->getUserAbsence($this->newSeason->id, $this->getAllGroupUsers[0]->id);
        $this->assertEquals(count($found), 5);
    }
 
    public function test_get_absence_of_a_season()
    {
        $this->createSeason();
        $found = $this->repo->getSeasonAbsence($this->newSeason->id);
        $this->assertEquals(count($found), 5);
    }

    public function test_get_season_absence_as_an_array_by_group_user()
    {
        $this->createSeason();
        $found = $this->repo->getSeasonAbsenceArray($this->newSeason->id);
        $count = count($found[$this->getAllGroupUsers[0]->id]['date']);
        $this->assertEquals($count, 5);
    }

    public function test_create_absence()
    {
        $this->createSeason();
        $data = [
            'season_id' => $this->newSeason->id,
            'date' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'group_user_id' => $this->getAllGroupUsers[0]->id,
        ];
        $absence = $this->repo->create($data,  $this->newSeason->id);

        $this->dataTests($data, $absence);
    }

    public function test_delete_absence()
    {
        $this->repo->delete($this->testData[0]->id);
        $found = $this->repo->getAllAbsences();
        $this->assertEquals(($this->recordCount - 1), count($found));
    }

    public function test_delete_all_absences_in_a_season()
    {
        $this->createSeason();
        $found = $this->repo->getSeasonAbsence($this->newSeason->id);
        $this->assertEquals(count($found), 5);
        $this->repo->deleteSeasonAbsence($this->newSeason->id);
        $found = $this->repo->getSeasonAbsence($this->newSeason->id);
        $this->assertEquals(count($found), 0);
    }
}