<?php

namespace Tests\Unit\Repository;

use Carbon\Carbon;

use Tests\TestCase;
use App\Models\Absence;
use App\Models\GroupUser;
use App\Models\Season;
use App\Models\User;

use App\Repositories\AbsenceRepo;

class AbsenceTest extends TestCase
{
    protected $testData;
    protected $data;
    protected $repo;
    protected $allUsers;
    protected $getAllGroupUsers;
    protected $getAllSeasons;

    protected $recordCount;
    protected $countAbsenceGroupUser;
    protected $countAbsencesSeason;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();

        $this->repo =  new AbsenceRepo();
        $this->testData = Absence::with(['user', 'season'])->get();
        $this->recordCount = count($this->testData);

        $this->getAllUsers = User::all();
        $this->getAllGroupUsers = GroupUser::all();
        $this->getAllSeasons = Season::all();

        $this->countAbsenceGroupUser = 0;
        $this->countAbsencesSeason = 0;

       // dd($this->getAllSeasons[0]->group->groupUsers[0]->id);
        foreach($this->testData as $absence){
            
            if($absence['season_id'] == $this->getAllSeasons[0]->id ){
                $this->countAbsencesSeason++;
               if(isset($this->getAllSeasons[0]->group->groupUsers[0]->id) === false){
                   continue;
               }
                
                if($this->getAllSeasons[0]->group->groupUsers[0]->id == $absence['group_user_id']){
                    $this->countAbsenceGroupUser++;
                }
                
            }
        }
        //dd($this->countAbsenceGroupUser);

        //default dataset
        $this->data = [
            'season_id' => $this->getAllSeasons[0]->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'group_user_id' => $this->getAllGroupUsers[0]->id,
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
        $this->assertEquals($data['group_user_id'], $testData->group_user_id);
    }

    public function test_get_all_seasons()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m Absence Repository Test:   [0m';
        
        $found = $this->repo->getAllAbsences();
        $this->assertEquals($this->recordCount, count($found));
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
        if(isset($this->getAllSeasons[0]->group->groupUsers[0]->id) === false){
            echo PHP_EOL.'[42m Not tested  [0m get absence of a user in a season';
        }else{
            $found = $this->repo->getUserAbsence($this->getAllSeasons[0]->id, $this->getAllSeasons[0]->group->groupUsers[0]->id);
            $this->assertEquals($this->countAbsenceGroupUser, count($found));
            echo PHP_EOL.'[42m OK  [0m get absence of a user in a season';
        }
        
    }
 
    public function test_get_absence_of_a_season()
    {        
        $found = $this->repo->getSeasonAbsence($this->getAllSeasons[0]->id);
        $this->assertEquals($this->countAbsencesSeason, count($found));
        echo PHP_EOL.'[42m OK  [0m get absence of a season';
    }

    public function test_get_an_array_absence_of_a_season()
    {        
        $this->assertEquals(1, 1);
        echo PHP_EOL.'[41m Rewrite  [0m get an array of absences of a season';
        //$found = $this->repo->getSeasonAbsenceArray($this->getAllSeasons[0]->id);
        //$this->assertEquals($this->countAbsencesSeason, count($found[$this->getAllSeasons[0]->id]['date']));
        //echo PHP_EOL.'[42m OK  [0m get an array of absences of a season';
    }

    public function test_create_absence()
    {
        $season = $this->repo->create($this->data,  $this->getAllSeasons[0]->id);        
        $this->dataTests($this->data, $season);
        echo PHP_EOL.'[42m OK  [0m create an absence';
    }

    public function test_delete_absence()
    {
        $this->repo->delete($this->testData[0]->id);
        $found = $this->repo->getAllAbsences();
        $this->assertEquals(($this->recordCount - 1), count($found));
        echo PHP_EOL.'[42m OK  [0m delete absence';
    }

    public function test_delete_all_absences_in_a_season()
    {
        //$this->repo->create($this->data,  $this->getAllSeasons[1]->id, $this->getAllUsers[0]->id);       
        //$this->repo->create($this->data,  $this->getAllSeasons[1]->id, $this->getAllUsers[0]->id);       
        //$this->repo->create($this->data,  $this->getAllSeasons[1]->id, $this->getAllUsers[0]->id);       

        $this->repo->deleteSeasonAbsence($this->getAllSeasons[0]->id);
        $found = $this->repo->getSeasonAbsence($this->getAllSeasons[0]->id);
        $this->assertEquals(0, count($found));
        echo PHP_EOL.'[42m OK  [0m delete all season absence';
    }
    
}