<?php

namespace Tests\Unit\Repository;

use Carbon\Carbon;

use Tests\TestCase;
use App\Models\Absence;
use App\Models\Season;
use App\Models\User;

use App\Repositories\AbsenceRepo;

class AbsenceTest extends TestCase
{
    protected $testData;
    protected $data;
    protected $repo;
    protected $allUsers;
    protected $getAllSeasons;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();

        $this->repo =  new AbsenceRepo();
        $this->testData = Absence::with(['user', 'season'])->get();

        $this->getAllUsers = User::all();
        $this->getAllSeasons = Season::all();

        //default dataset
        $this->data = [
            'season_id' => $this->getAllSeasons[0]->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'user_id' => $this->getAllUsers[0]->id,
        ];
    }

    /**
     * Default data test
     */
    protected function dataTests($data, $testData) : void
    {
        $this->assertInstanceOf(Absence::class, $testData);
        $this->assertEquals($data['season_id'], $testData->season_id);
        $this->assertEquals($data['date'], $testData->date);
        $this->assertEquals($data['user_id'], $testData->user_id);
    }

    public function test_get_all_seasons()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m Absence Repository Test:   [0m';
        
        $found = $this->repo->getAllAbsences();
        $this->assertEquals(10, count($found));
        echo PHP_EOL.'[42m OK  [0m get all absences';
    }
    
    public function test_get_absence()
    {        
        $found = $this->repo->getAbsence($this->testData[0]->id);
        $this->dataTests($this->testData[0], $found);
        echo PHP_EOL.'[42m OK  [0m get absence';
    }

    public function test_get_absence_of_a_user_in_a_season()
    {        
        $found = $this->repo->getUserAbsence($this->getAllSeasons[0]->id, $this->getAllUsers[0]->id);
        $this->assertEquals(10, count($found));
        echo PHP_EOL.'[42m OK  [0m get absence of a user in a season';
    }

    public function test_get_absence_of_a_season()
    {        
        $found = $this->repo->getSeasonAbsence($this->getAllSeasons[0]->id);
        $this->assertEquals(10, count($found));
        echo PHP_EOL.'[42m OK  [0m get absence of a season';
    }

    public function test_get_an_array_absence_of_a_season()
    {        
        $found = $this->repo->getSeasonAbsenceArray($this->getAllSeasons[0]->id);
        $this->assertEquals(10, count($found[$this->getAllSeasons[0]->id]['date']));
        echo PHP_EOL.'[42m OK  [0m get an array of absences of a season';
    }

    public function test_create_season()
    {
        $season = $this->repo->create($this->data,  $this->getAllSeasons[0]->id, $this->getAllUsers[0]->id);        
        $this->dataTests($this->data, $season);
        echo PHP_EOL.'[42m OK  [0m create an absence';
    }

    public function test_delete_absence()
    {
        $this->repo->delete($this->testData[0]->id);
        $found = $this->repo->getAllAbsences();
        $this->assertEquals(9, count($found));
        echo PHP_EOL.'[42m OK  [0m delete absence';
    }

    public function test_delete_all_absences_in_a_season()
    {
        $this->repo->create($this->data,  $this->getAllSeasons[1]->id, $this->getAllUsers[0]->id);       
        $this->repo->create($this->data,  $this->getAllSeasons[1]->id, $this->getAllUsers[0]->id);       
        $this->repo->create($this->data,  $this->getAllSeasons[1]->id, $this->getAllUsers[0]->id);       

        $this->repo->deleteSeasonAbsence($this->getAllSeasons[0]->id);
        $found = $this->repo->getAllAbsences();
        $this->assertEquals(3, count($found));
        echo PHP_EOL.'[42m OK  [0m delete all season absence';
    }
}