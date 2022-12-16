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
use App\Services\GroupUserService;
use App\Services\SeasonGeneratorService\GeneratorFactory;
use App\Services\SeasonService;
use Auth;
use Database\Seeders\GeneratorSeeder;
use Laravel\Sanctum\Sanctum;

class SeasonTest extends TestCase
{
    protected $allUsers;
    protected $allSeasons;
    protected $allGroups;
    protected $allGroupUsers;

    protected $newSeason;

    protected $seasonCount;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->allUsers = User::all();
        $this->allSeasons = Season::all();
        $this->allGroups = Group::all();
        $this->allGroupUsers = GroupUser::all();

        $this->seasonCount = count($this->allSeasons);
    }

    /**
     *  Get authenticated user
     */
    protected function authenticatedUser($role = "Admin"){
        $user = Sanctum::actingAs(
            $this->allUsers[0]
        );
        $user->role = $role;
        return $user;
    }

    protected function createGeneratedSeason($public = 0){
        $seasonService = new SeasonService();
        //create new season
        $seasonData = Season::factory()->make(['begin' => Carbon::now()->addDays(1)->format('Y-m-d')])->toArray();
        $seasonData['public'] = $public;
        $seasonData['admin_id'] = $this->allUsers[0]->id;
        $seasonData['day'] = $seasonService->getDutchDay($seasonData['begin']);
        $this->newSeason = Season::create($seasonData);

        //add a registered user to a group
        if($this->allGroupUsers[0]->user_id == null && $public == 0){
            $groupUserService = new GroupUserService();
            $groupUser = $groupUserService->regenerateGroupUserCode($this->allGroupUsers[0]);
            $groupUser = $groupUserService->joinGroup($groupUser->code, Auth::user()->id);
        }
        $this->allGroupUsers = GroupUser::all();
        
        //set some absences
        for($x = 1; $x <= 20;  $x++){
            $randomDay = rand(1, 30);
            $randomGroupUser = rand(0, 9);
            $data = [
                'season_id' => $this->newSeason->id,
                'date' => Carbon::now()->addDays(7*$randomDay)->format('Y-m-d'),
                'group_user_id' => $this->allGroupUsers[$randomGroupUser]->id,
            ];
            Absence::create($data);
        }

        $seasonGenerator = GeneratorFactory::generate($this->newSeason->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->newSeason);
        $seasonGenerator->saveSeason(json_encode($generatedSeason));
    }

     /**
     * Default data test
     */
    protected function SeasonDataTests($data, $testData) : void
    {
        $this->assertEquals($data['name'], $testData->name);
        $this->assertEquals($data['begin'], $testData->begin);
        $this->assertEquals($data['end'], $testData->end);
        $this->assertEquals($data['group_id'], $testData->group_id);
        $this->assertEquals($data['admin_id'], $testData->admin_id);
        $this->assertEquals($data['start_hour'], $testData->start_hour);
        $this->assertEquals($data['type'], $testData->type);
        $this->assertEquals($data['typeMember'], $testData->type_member);
        
        $this->assertEquals($data['public'], $testData->public);
        $this->assertEquals($data['is_generated'], $testData->is_generated);
        $this->assertEquals($data['allow_replacement'], $testData->allow_replacement);
    }

    protected function GeneratedSeasonTests($generatedSeason){
        $this->assertObjectHasAttribute('absenceData', $generatedSeason);
        $this->assertObjectHasAttribute('currentPlayDay', $generatedSeason);

        $this->assertObjectHasAttribute('data', $generatedSeason);
        $this->assertObjectHasAttribute('day', $generatedSeason->data[0]);
        $this->assertObjectHasAttribute('user', $generatedSeason->data[0]);
        $this->assertEquals(count(json_decode(json_encode($generatedSeason->data[0]->user), true)), 10);

        $this->assertObjectHasAttribute('groupUserData', $generatedSeason);
        $this->assertEquals(count(json_decode(json_encode($generatedSeason->groupUserData), true)), 10);

        $this->assertObjectHasAttribute('seasonData', $generatedSeason);

        $this->assertObjectHasAttribute('stats', $generatedSeason);
        $groupUserId = $this->allGroupUsers[0]->id;
        $stats = json_decode(json_encode($generatedSeason->stats), true);
        $this->assertArrayHasKey('team1', $stats[$groupUserId]);
        $this->assertArrayHasKey('team2', $stats[$groupUserId]);
        $this->assertArrayHasKey('team3', $stats[$groupUserId]);
        $this->assertArrayHasKey('total', $stats[$groupUserId]);
    }
    
    public function test_test_season(){
        $this->assertEquals(1, 1);
    }

    public function test_season_controller_index()
    {
        $this->be($this->authenticatedUser());

        $response = $this->get('/api/season');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();
        $this->assertEquals(count($response_data->data), $this->seasonCount);

        $this->SeasonDataTests($this->allSeasons[0], $response_data->data[0]);
    }

    public function test_season_controller_store()
    {
        $this->be($this->authenticatedUser());

        $data = Season::factory()->make()->toArray();      

        $response = $this->postJson('/api/season', $data);
        $response_data = $response->getData();
       
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->seasonDataTests($data, $response_data);
    }

    public function test_season_controller_show()
    {
        $this->be($this->authenticatedUser());
        $seasonId = $this->allSeasons[0]->id;

        $response = $this->get('/api/season/'.$seasonId);
        $response_data = $response->getData();
       
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $this->SeasonDataTests($this->allSeasons[0], $response_data);
    }

    public function test_season_controller_update()
    {
        $this->be($this->authenticatedUser());

        $data = Season::factory()->make()->toArray();      

        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id, $data);
        $response_data = $response->getData();
       
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->seasonDataTests($data, $response_data);
    }

    public function test_season_controller_destroy()
    {
        $this->be($this->authenticatedUser());

        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id.'/delete');
        $response->assertStatus(202);
        $this->assertEquals(202, $response->status());

        $response = $this->get('/api/season');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();
        $this->assertEquals(count($response_data->data), ($this->seasonCount - 1));
    }

    public function test_season_controller_season_is_generated()
    {
        $this->be($this->authenticatedUser());

        $response = $this->get('/api/season/'.$this->allSeasons[0]->id.'/is_generated');
        $response_data = $response->getData();

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(1, $response_data->is_generated);
    }

    public function test_active_season_controller_index()
    {
        $this->be($this->authenticatedUser());

        $this->createGeneratedSeason();
        $this->assertEquals(count(Season::all()), ($this->seasonCount + 1));

        $response = $this->get('/api/active_seasons');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();
        $this->assertEquals(1, count($response_data->data));
    }

    public function test_absence_controller_index()
    {
        $this->be($this->authenticatedUser());

        $response = $this->get('/api/season/'.$this->allSeasons[0]->id.'/absence');

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
    }

    public function test_AbsenceController_store()
    {
        $this->be($this->authenticatedUser());

        $data = [
            'date' => Carbon::now()->addDays(8)->format('Y-m-d'),
            'group_user_id' => $this->allGroupUsers[0]->id,
        ];

        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id.'/absence', $data);
        $response_data = $response->getData();

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($data['date'], $response_data->date);
        $this->assertEquals($this->allGroupUsers[0]->id, $response_data->group_user_id);
        $this->assertEquals($this->allSeasons[0]->id, $response_data->season_id);
    }

    public function test_absence_controller_delete()
    {

        $this->be($this->authenticatedUser());
        //create an absence
        $data = [
            'date' => Carbon::now()->addDays(8)->format('Y-m-d'),
            'group_user_id' => $this->allGroupUsers[0]->id,
        ];

        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id.'/absence', $data);
        $response_data = $response->getData();

        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id.'/absence/'.$response_data->id.'/delete');
        $response_data = $response->getData();

        $this->assertEquals(204, $response->status());
        $this->assertEquals('absence is removed', $response_data);
    }

    public function test_season_generator_controller_index()
    {
        $this->be($this->authenticatedUser());

        $this->createGeneratedSeason();

        $response = $this->get('/api/season/'.$this->newSeason->id.'/generator');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();

        $this->GeneratedSeasonTests($response_data);
    }

    public function test_season_generator_controller_public()
    {
        //public season return a code 200
        $this->createGeneratedSeason(1);
        $response = $this->get('/api/season/'.$this->newSeason->id.'/public');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        //non public season return a code 203
        $response = $this->get('/api/season/'.$this->allSeasons[0]->id.'/public');
        $response->assertStatus(203);
        $this->assertEquals(203, $response->status());
    }

    public function test_season_generator_controller_generate_season()
    {
        $this->be($this->authenticatedUser());

        $response = $this->get('/api/season/'.$this->allSeasons[0]->id.'/generator/new');

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();
        $this->GeneratedSeasonTests($response_data);
    }

    public function test_season_generator_controller_store()
    {
        $this->be($this->authenticatedUser());

        //generate a new season
        $response = $this->get('/api/season/'.$this->allSeasons[0]->id.'/generator/new');

        //store the generated season
        $response_data['jsonSeason'] = json_encode($response->getData());
        $response = $this->postJson('/api/season/'.$this->allSeasons[0]->id.'/generator', $response_data);

        $response_data = $response->getData();
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals('season is made', $response_data);

        //test the saved season
        $response = $this->get('/api/season/'.$this->allSeasons[0]->id.'/generator');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();
        $this->GeneratedSeasonTests($response_data);
    }
    
    //Create 2 new seasons and safe the second season to the first season
    public function test_season_generator_controller_update()
    {
        $this->be($this->authenticatedUser());
        $this->createGeneratedSeason();
        $season1 = $this->newSeason;

        $this->createGeneratedSeason();
        $season2 = $this->newSeason;

        //first season
        $this->get('/api/season/'.$season1->id.'/generator');

        //test the second season
        $response = $this->get('/api/season/'.$season2->id.'/generator');
        $response_data = $response->getData();

        //save the second season to the first season
        $response_data = $response_data->data;
        $response_data['teamRange'] = json_encode($response_data);
        $response = $this->postJson('/api/season/'.$season1->id.'/generator/'.$season1->id, $response_data);
        
        $response_data = $response->getData();
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals('season is updated', $response_data);
        
        //test the saved season
        $response = $this->get('/api/season/'.$season1->id.'/generator');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();
        $this->GeneratedSeasonTests($response_data);
    }

    public function test_season_generator_controller_destroy()
    {
        $this->be($this->authenticatedUser());
        $this->createGeneratedSeason();

        $response = $this->postJson('/api/season/'.$this->newSeason->id.'/generator/'.$this->newSeason->id.'/delete');
        $response_data = $response->getData();

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Calendar is deleted', $response_data);
    }

    public function test_season_generator_controller_play_dates()
    {
        $this->be($this->authenticatedUser());
        $this->createGeneratedSeason();

        $response = $this->get('/api/season/'.$this->newSeason->id.'/generator/play_dates');
        $response_data = $response->getData();

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertObjectHasAttribute('data', $response_data);

        $beginDate = new Carbon($this->newSeason->begin);
        foreach($response_data->data AS $date){
            $this->assertEquals($date->date, $beginDate->format('Y-m-d'));
            $beginDate->add(new \DateInterval('P7D'));
        }
    }

    public function test_season_generator_controler_create_empty_season(){
        $this->be($this->authenticatedUser());

        $response = $this->get('/api/season/'.$this->allSeasons[0]->id.'/generator/create_empty_season');
        $response_data = $response->getData();

        $this->assertObjectHasAttribute('absenceData', $response_data);
        $this->assertObjectHasAttribute('currentPlayDay', $response_data);
        $this->assertObjectHasAttribute('data', $response_data);
        $this->assertObjectHasAttribute('day', $response_data->data[0]);
        $this->assertObjectHasAttribute('user', $response_data->data[0]);
        $this->assertEquals(count(json_decode(json_encode($response_data->data[0]->user), true)), 10);
        $this->assertObjectHasAttribute('groupUserData', $response_data);
        $this->assertEquals(count(json_decode(json_encode($response_data->groupUserData), true)), 10);
        $this->assertObjectHasAttribute('seasonData', $response_data);
        $this->assertObjectHasAttribute('stats', $response_data);
    }

    public function test_team_controller_update_range()
    {
        $this->be($this->authenticatedUser());
        $this->createGeneratedSeason();

        $response = $this->get('/api/season/'.$this->newSeason->id.'/generator');
        $response_data = $response->getData();

        //update the season range
        $updateRange = array();
        $x=0;
        foreach($response_data->data[0]->teams AS $key=>$team){
            $updateRange[$key] = $this->allGroupUsers[$x]->id;
            $x++;
        }
        $data['teamRange'] = json_encode($updateRange);
        $this->post('/api/season/'.$this->newSeason->id.'/team/range', $data);
        

        //get the updated season
        $response = $this->get('/api/season/'.$this->newSeason->id.'/generator');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();
        $this->GeneratedSeasonTests($response_data);

        //test the updated data
        $x=0;
        foreach($response_data->data[0]->teams AS $key=>$team){
            $this->assertEquals($team->groupUserId, $this->allGroupUsers[$x]->id);
            $x++;
        }
    }

    public function test_team_controller_ask_for_replacement()
    {
        $this->be($this->authenticatedUser());
        $this->createGeneratedSeason();

        $teams = Team::where('season_id', $this->newSeason->id)->get();

        //make sure the first team has the logged in user
        $updateRange = array();
        $updateRange[$teams[0]->id] = $this->allGroupUsers[0]->id;
        $data['teamRange'] = json_encode($updateRange);
        $this->post('/api/season/'.$this->newSeason->id.'/team/range', $data);
        
        //ask for the replacement
        $response = $this->postJson('/api/season/'.$this->newSeason->id.'/team/'.$teams[0]->id.'/ask_for_replacement');
        $response_data = $response->getData();

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals('Ask for replacement is set to true', $response_data);

        //check if the ask for replacement succeeded
        $teams = Team::where('season_id', $this->newSeason->id)->get();
        $this->assertEquals($this->allGroupUsers[0]->id, $teams[0]->group_user_id);
        $this->assertEquals(1, $teams[0]->ask_for_replacement);
    }

    public function test_team_controller_cancel_request_for_replacement()
    {
        $this->be($this->authenticatedUser());
        $this->createGeneratedSeason();

        $teams = Team::where('season_id', $this->newSeason->id)->get();

        //make sure the first team has the logged in user
        $updateRange = array();
        $updateRange[$teams[0]->id] = $this->allGroupUsers[0]->id;
        $data['teamRange'] = json_encode($updateRange);
        $response = $this->post('/api/season/'.$this->newSeason->id.'/team/range', $data);
        
        //ask for the replacement
        $response = $this->postJson('/api/season/'.$this->newSeason->id.'/team/'.$teams[0]->id.'/ask_for_replacement');
        $response_data = $response->getData();

        //cancel replacement
        $response = $this->postJson('/api/season/'.$this->newSeason->id.'/team/'.$teams[0]->id.'/cancel_request_for_replacement');

        $response_data = $response->getData();

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($response_data, "Replacement is set to false");
    }

    public function test_team_controller_confirm_replacement()
    {
        $this->be($this->authenticatedUser());
        $this->createGeneratedSeason();

        $teams = Team::where('season_id', $this->newSeason->id)->get();

        //make sure the first team has the logged in user
        $updateRange = array();
        $updateRange[$teams[0]->id] = $this->allGroupUsers[0]->id;
        $data['teamRange'] = json_encode($updateRange);
        $this->post('/api/season/'.$this->newSeason->id.'/team/range', $data);

        
        //ask for the replacement
        $this->postJson('/api/season/'.$this->newSeason->id.'team/'.$teams[0]->id.'/ask_for_replacement');

        //update teams so another user is in the replacement place
        $response = $this->get('/api/season/'.$this->newSeason->id.'/generator');
        $response_data = $response->getData();

        $updateRange = array();

        $x=1;
        foreach($response_data->data[0]->teams AS $key=>$team){
            $updateRange[$key] = $this->allGroupUsers[$x]->id;
            $x++;
        }
        $data['teamRange'] = json_encode($updateRange);
        $this->post('/api/season/'.$this->newSeason->id.'/team/range', $data);

        //confirm replacement
        $response = $this->postJson('/api/season/'.$this->newSeason->id.'/team/'.$teams[0]->id.'/confirm_replacement');
        $response_data = $response->getData();

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($response_data, "Confirm replacement");

        $teams = Team::where('season_id', $this->newSeason->id)->get();
        $this->assertEquals($this->allGroupUsers[0]->id, $teams[0]->group_user_id);
        $this->assertEquals(0, $teams[0]->ask_for_replacement);
    }

}