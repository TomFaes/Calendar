<?php

namespace Tests\Unit\Route;

use App\Models\Absence;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Season;
use App\Models\Team;

use Carbon\Carbon;

use Tests\TestCase;

use App\Models\User;
use App\Repositories\TeamRepo;
use Laravel\Passport\Passport;
use App\Services\SeasonGeneratorService\GeneratorFactory;

class SeasonTest extends TestCase
{
    protected $allUsers;
    protected $allSeasons;
    protected $allAbsences;
    protected $allGroupUsers;
    protected $allTeams;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();

         $this->allUsers = User::all();
        $this->allSeasons = Season::all();
        $this->allGroups = Group::all();
        $this->allGroupUsers = GroupUser::all();
        $this->allAbsences = Absence::all();
        $this->allTeams = Team::all();
    }

    /**
     *  Get authenticated user
     */
    protected function authenticatedUser($role = "Admin"){
        $user = Passport::actingAs(
            $this->allUsers[0],
            ['create-servers']
        );
        $user->role = $role;
        return $user;
    }

     /**
     * Default data test
     */
    protected function SeasonDataTests($data, $testData) : void
    {
        $this->assertEquals($data['name'], $testData['name']);
        $this->assertEquals($data['begin'], $testData['begin']);
        $this->assertEquals($data['end'], $testData['end']);
        $this->assertEquals($data['group_id'], $testData['group_id']);
        $this->assertEquals($data['admin_id'], $testData['admin_id']);
        $this->assertEquals($data['start_hour'], $testData['start_hour']);
        $this->assertEquals($data['type'], $testData['type']);
    }
    
    public function test_test_season(){
        $this->assertEquals(1, 1);
        echo PHP_EOL.'[41m Rewrite  [0m Rewrite all season test';
    }
/*
    public function test_SeasonController_index()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m Season api routest:   [0m';

        $this->be($this->authenticatedUser());

        $response = $this->get('/api/season');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->decodeResponseJson(); 
        $this->assertEquals(count($response_data), 10);

        echo PHP_EOL.'[42m OK  [0m test index in the SeasonController';
    }

    
    public function test_SeasonController_store()
    {
        $this->be($this->authenticatedUser());

        $data = [
            'name' => 'a season name',
            'begin' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'end' =>  Carbon::now()->addMonths(1)->format('Y-m-d'),
            'group_id' =>  $this->allGroups[0]->id,
            'admin_id' => $this->allUsers[0]->id,
            'start_hour' => "20:00",
            'type' => 'TwoFieldTwoHourThreeTeams'
        ];

        $response = $this->postJson('/api/season', $data);
        $response_data = $response->decodeResponseJson(); 
       
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->seasonDataTests($data, $response_data);

        echo PHP_EOL.'[42m OK  [0m test store method in the SeasonController';
    }

    public function test_SeasonController_update()
    {
        $this->be($this->authenticatedUser());

        $data = [
            'name' => 'update a season name',
            'begin' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'end' =>  Carbon::now()->addMonths(1)->format('Y-m-d'),
            'group_id' =>  $this->allGroups[0]->id,
            'admin_id' => $this->allUsers[0]->id,
            'start_hour' => "20:00",
            'type' => 'TwoFieldTwoHourThreeTeams'
        ];

        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id, $data);
        $response_data = $response->decodeResponseJson(); 
       
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->seasonDataTests($data, $response_data);

        echo PHP_EOL.'[42m OK  [0m test update method in the SeasonController';
    }

    public function test_SeasonController_destroy()
    {
        $this->be($this->authenticatedUser());

        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id.'/delete');
        $response->assertStatus(204);
        $this->assertEquals(204, $response->status());

        $response = $this->get('/api/season');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->decodeResponseJson(); 
        $this->assertEquals(count($response_data), 9);

        echo PHP_EOL.'[42m OK  [0m test destroy method in the SeasonController';
    }

    public function test_ActiveSeasonController_index()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m active Season api routest:   [0m';

        $this->be($this->authenticatedUser());

        $data = [
            'firstname' => 'firstname',
            'name' => 'name',
            'email' => 'test@test.be',
            'group_id' => $this->allSeasons[0]->group_id,
            'user_id' => $this->allUsers[0]->id,
        ];

        //generate a season
        $response = $this->postJson('/api/group/'.$this->allSeasons[0]->group_id.'/user', $data);
        $seasonGenerator = GeneratorFactory::generate($this->allSeasons[0]->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->allSeasons[0]);
        $jsonSeason = json_encode($generatedSeason);
        $seasonGenerator->saveSeason($jsonSeason);

        //generate a second season
        $response = $this->postJson('/api/group/'.$this->allSeasons[1]->group_id.'/user', $data);
        $seasonGenerator = GeneratorFactory::generate($this->allSeasons[1]->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->allSeasons[1]);
        $jsonSeason = json_encode($generatedSeason);
        $seasonGenerator->saveSeason($jsonSeason);
       
        $response = $this->get('/api/active_seasons');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->decodeResponseJson(); 
        $this->assertEquals(count($response_data['season']), 2);

        echo PHP_EOL.'[42m OK  [0m test index in the ActiveSeasonController';
    }

    public function test_SeasonGeneratorController_index()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m Season generator api routest:   [0m';

        $this->be($this->authenticatedUser());

        $data = [
            'firstname' => 'firstname',
            'name' => 'name',
            'email' => 'test@test.be',
            'group_id' => $this->allSeasons[0]->group_id,
            'user_id' => $this->allUsers[0]->id,
        ];

        //generate a season
        $response = $this->postJson('/api/group/'.$this->allSeasons[0]->group_id.'/user', $data);
        $seasonGenerator = GeneratorFactory::generate($this->allSeasons[0]->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->allSeasons[0]);
        $jsonSeason = json_encode($generatedSeason);
        $seasonGenerator->saveSeason($jsonSeason);
       
        $response = $this->get('/api/season/'.$this->allSeasons[0]->id.'/generator');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        echo PHP_EOL.'[42m OK  [0m test index in the SeasonGeneratorController';
    }

    public function test_SeasonGeneratorController_store()
    {
        $this->be($this->authenticatedUser());

        $data = [
            'firstname' => 'firstname',
            'name' => 'name',
            'email' => 'test@test.be',
            'group_id' => $this->allSeasons[0]->group_id,
            'user_id' => $this->allUsers[0]->id,
        ];

        //generate a season
        $response = $this->postJson('/api/group/'.$this->allSeasons[0]->group_id.'/user', $data);
        $seasonGenerator = GeneratorFactory::generate($this->allSeasons[0]->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->allSeasons[0]);

        $generatedSeason['jsonSeason'] = json_encode($generatedSeason);
        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id.'/generator', $generatedSeason);
        
        $response_data = $response->decodeResponseJson(); 
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals('season is made', $response_data);

        echo PHP_EOL.'[42m OK  [0m test store in the SeasonGeneratorController';
    }

    public function test_SeasonGeneratorController_update()
    {
        $this->be($this->authenticatedUser());
        $data['test'] = "leeg";
        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id.'/generator/1', $data);
        
        $response_data = $response->decodeResponseJson(); 
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals('to be made update', $response_data);

        echo PHP_EOL.'[42m OK  [0m test update in the SeasonGeneratorController';
    }

    public function test_SeasonGeneratorController_destroy()
    {
        $this->be($this->authenticatedUser());
        $data['test'] = "leeg";
        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id.'/generator/'.$this->allSeasons[0]->id.'/delete');
        
        $response_data = $response->decodeResponseJson(); 
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals('to be made destroy', $response_data);

        echo PHP_EOL.'[42m OK  [0m test delete in the SeasonGeneratorController';
    }

    public function test_SeasonController_playDates()
    {
        $this->be($this->authenticatedUser());

        $response = $this->get('/api/season/'.$this->allSeasons[0]->id.'/generator/new');

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        echo PHP_EOL.'[42m OK  [0m test generate new season in the SeasonController';
    }

    public function test_AbsenceController_index()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m absence api routest:   [0m';

        $this->be($this->authenticatedUser());

        $response = $this->get('/api/season/'.$this->allSeasons[0]->id.'/absence');

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        echo PHP_EOL.'[42m OK  [0m test index in the AbsenceController';
    }

    public function test_AbsenceController_store()
    {
        $this->be($this->authenticatedUser());

        $data = [
            'firstname' => 'new firstname',
            'name' => 'new name',
            'email' => 'newtest@test.be',
            'group_id' => $this->allGroupUsers[0]->group_id,
            //'user_id' => $this->allUsers[0]->id,
            'group_user_id' => $this->allGroupUsers[0]->id,
        ];

        $this->postJson('/api/group/'.$this->allGroupUsers[0]->group_id.'/user/'.$this->allGroupUsers[0]->id, $data);
        $this->allGroupUsers = GroupUser::All();

        $data = [
            'name' => 'a season name',
            'begin' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'end' =>  Carbon::now()->addMonths(1)->format('Y-m-d'),
            'group_id' =>  $this->allGroupUsers[0]->group_id,
            'admin_id' => $this->allUsers[0]->id,
            'start_hour' => "20:00",
            'type' => 'TwoFieldTwoHourThreeTeams'
        ];

        $newSeason = $this->postJson('/api/season', $data);
        $newSeason = $newSeason->decodeResponseJson(); 

        $data = [
            'date' => Carbon::now()->addDays(8)->format('Y-m-d'),
            'group_user_id' => $this->allGroupUsers[0]->id,
        ];

        $response = $this->postJson('/api/season/'.$newSeason['id'].'/absence', $data);
        $response_data = $response->decodeResponseJson(); 

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($data['date'], $response_data['date']);

        $this->assertEquals($this->allGroupUsers[0]->id, $response_data['group_user_id']);
        $this->assertEquals($newSeason['id'], $response_data['season_id']);

        echo PHP_EOL.'[42m OK  [0m test store method in the AbsenceController';
    }

    public function test_TeamController_askForReplacement()
    {
        $this->be($this->authenticatedUser());

        //set the logged in user as group user
        foreach($this->allGroupUsers AS $key => $groupUser){
            if($groupUser->id == $this->allTeams[0]->group_user_id){
                $data = [
                    'firstname' => 'new firstname',
                    'name' => 'new name',
                    'email' => 'newtest@test.be',
                    'group_id' => $this->allGroupUsers[0]->group_id,
                    'user_id' =>  $this->allUsers[0]->id,
                ];
                
                $this->postJson('/api/group/'.$groupUser->group_id.'/user/'.$groupUser->id, $data);
                $this->allGroupUsers = GroupUser::All();
                 break;
            }
        }
        
        $response = $this->postJson('/api/team/'.$this->allTeams[0]->id.'/askForReplacement');
        $response_data = $response->decodeResponseJson(); 

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($response_data, "Ask for replacement is set to true");

        echo PHP_EOL.'[42m OK  [0m test ask for replacement method in the TeamController';
    }

    public function test_TeamController_cancelRequestForReplacement()
    {
        $this->be($this->authenticatedUser());

        //set a request for replacement to true
        foreach($this->allGroupUsers AS $key => $groupUser){
            if($groupUser->id == $this->allTeams[0]->group_user_id){
                $data = [
                    'firstname' => 'new firstname',
                    'name' => 'new name',
                    'email' => 'newtest@test.be',
                    'group_id' => $this->allGroupUsers[0]->group_id,
                    'user_id' =>  $this->allUsers[0]->id,
                ];
                
                $this->postJson('/api/group/'.$groupUser->group_id.'/user/'.$groupUser->id, $data);
                $this->allGroupUsers = GroupUser::All();
                 break;
            }
        }
        $response = $this->postJson('/api/team/'.$this->allTeams[0]->id.'/askForReplacement');
        $response_data = $response->decodeResponseJson(); 

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($response_data, "Ask for replacement is set to true");

        //Cancel the request for replacement
        $response = $this->postJson('/api/team/'.$this->allTeams[0]->id.'/cancelRequestForReplacement');
        $response_data = $response->decodeResponseJson(); 

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($response_data, "Replacement is set to false");
        echo PHP_EOL.'[42m OK  [0m test cancel request for replacement method in the TeamController';
    }

    public function test_TeamController_confirmReplacement()
    {
        $this->be($this->authenticatedUser());

        //set a request for replacement to true
        foreach($this->allGroupUsers AS $key => $groupUser){
            if($groupUser->id == $this->allTeams[0]->group_user_id){
                $data = [
                    'firstname' => 'new firstname',
                    'name' => 'new name',
                    'email' => 'newtest@test.be',
                    'group_id' => $this->allGroupUsers[0]->group_id,
                    'user_id' =>  $this->allUsers[0]->id,
                ];
                
                $this->postJson('/api/group/'.$groupUser->group_id.'/user/'.$groupUser->id, $data);
                $this->allGroupUsers = GroupUser::All();
                 break;
            }
        }
        $response = $this->postJson('/api/team/'.$this->allTeams[0]->id.'/askForReplacement');
        $response_data = $response->decodeResponseJson(); 

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($response_data, "Ask for replacement is set to true");

        //add a new user to a group user to test replacement
        $data = [
            'firstname' => 'new firstname',
            'name' => 'new name',
            'email' => 'newtest@test.be',
            'group_id' => $this->allGroupUsers[1]->group_id,
            'user_id' =>  $this->allUsers[1]->id,
        ];
        $this->postJson('/api/group/'.$this->allGroupUsers[1]->group_id.'/user/'.$this->allGroupUsers[1]->id, $data);
        
        //change logged in user
        $user = Passport::actingAs(
            $this->allUsers[1],
            ['create-servers']
        );
        $user->role = "Admin";
        $this->be($user);

        $response = $this->postJson('/api/team/'.$this->allTeams[0]->id.'/confirmReplacement');
        $response_data = $response->decodeResponseJson(); 

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($response_data, "Confirm replacement");
        echo PHP_EOL.'[42m OK  [0m test confirm replacement method in the TeamController';
    }
*/
}