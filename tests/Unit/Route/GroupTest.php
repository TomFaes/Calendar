<?php

namespace Tests\Unit\Route;

use App\Models\Group;
use App\Models\GroupUser;
use Tests\TestCase;

use App\Models\User;

use Database\Seeders\GeneratorSeeder;
use Laravel\Sanctum\Sanctum;

class GroupTest extends TestCase
{
    protected $allUsers;
    protected $allGroups;
    protected $allGroupUsers;
    protected $recordCount;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->allUsers = User::all();
        $this->allGroups = Group::all();
        $this->allGroupUsers = GroupUser::all();

        $this->recordCount = count($this->allGroups);
    }

    /**
     *  Get authenticated user
     */
    protected function authenticatedUser($role = "Admin"){
        $user = Sanctum::actingAs(
            $this->allUsers[0],
            ['create-servers']
        );
        $user->role = $role;
        return $user;
    }

     /**
     * Default data test
     */
    
    protected function GroupDataTests($data, $testData) : void
    {
        
        $this->assertEquals($data['name'], $testData->name);
        $this->assertEquals($data['admin_id'], $testData->admin_id);
    }

    public function GroupUserDataTests($data, $testData) : void
    {
        $this->assertEquals($data['firstname'], $testData->firstname);
        $this->assertEquals($data['name'], $testData->name);
        $this->assertEquals($data['group_id'], $testData->group_id);
    }

    public function test_group_controller_store()
    {
        $this->be($this->authenticatedUser());
        $data = Group::factory()->make(['admin_id' => $this->allUsers[0]->id])->toArray();
        
        $response = $this->postJson('/api/group', $data);
        $response_data = $response->getData();

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->GroupDataTests($data, $response_data);
    }

    public function test_group_controller_show()
    {
        $this->be($this->authenticatedUser());

        $response = $this->get('/api/group/'.$this->allGroups[0]->id);
        $response_data = $response->getData();
       
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->GroupDataTests($this->allGroups[0], $response_data);
    }

   
    public function test_group_controller_update()
    {
        $this->be($this->authenticatedUser());

        
        $data = Group::factory()->make(['admin_id' => $this->allUsers[0]->id])->toArray();

        $response = $this->postJson('/api/group/'.$this->allGroups[0]->id, $data);
        $response_data = $response->getData();
       
        $response->assertStatus(201);
        $this->assertEquals(201, $response->status());
        $this->GroupDataTests($data, $response_data);
    }

    public function test_group_controller_destroy()
    {
        $this->be($this->authenticatedUser());

        //create group
        $this->be($this->authenticatedUser());
        $data = Group::factory()->make(['admin_id' => $this->allUsers[0]->id])->toArray();
        $group = $this->postJson('/api/group', $data);

        //delete a group
        $response = $this->postJson('/api/group/'.$group['id'].'/delete');
        $response->assertStatus(202);
        $this->assertEquals(202, $response->status());
        $this->assertEquals( $this->recordCount,count(Group::all()));
    }

    public function test_user_groups_controller_index()
    {
        $this->be($this->authenticatedUser());

        $response = $this->get('/api/user-group');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();

        $this->assertEquals(count($response_data->data), 2);
        $this->GroupDataTests($this->allGroups[0], $response_data->data[0]);
    }

    public function test_group_users_controller_index()
    {
        $this->be($this->authenticatedUser());

        $response = $this->get('/api/group/'.$this->allGroups[0]->id."/users");
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();
        $this->assertEquals(count($response_data->data), 10);

        $groupUserId = $response_data->data[0]->id;
        $groupUser = GroupUser::where('id', $groupUserId)->get();
        $groupUser = json_decode(json_encode($groupUser), true);

        $this->GroupUserDataTests($groupUser[0], $response_data->data[0]);
    }

    public function test_group_users_controller_store()
    {
        $this->be($this->authenticatedUser());

        $data = GroupUser::factory()->make([
            'group_id' => $this->allGroupUsers[0]->group_id_id, 
            'user_id' => $this->allUsers[0]->id
            ])->toArray();

        $response = $this->postJson('/api/group/'.$this->allGroupUsers[0]->group_id.'/user', $data);
        $response_data = $response->getData();
       
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->groupUserDataTests($data, $response_data);

        $this->assertNotEquals(null, $response_data->code);
    }

    public function test_group_users_controller_update()
    {
        $this->be($this->authenticatedUser());

        
        $data = GroupUser::factory()->make([
            'group_id' => $this->allGroupUsers[1]->group_id, 
            'user_id' => $this->allUsers[1]->id
            ])->toArray();

        $response = $this->postJson('/api/group/'.$this->allGroupUsers[0]->group_id.'/user/'.$this->allGroupUsers[0]->id, $data);
        $response_data = $response->getData();
       
        $response->assertStatus(201);
        $this->assertEquals(201, $response->status());
        $this->groupUserDataTests($data, $response_data);
        $this->assertNotEquals(null, $response_data->group_id);
        $this->assertNotEquals(null, $response_data->user_id);
    }

    public function test_group_users_controller_destroy()
    {
        $this->be($this->authenticatedUser());

        $response = $this->postJson('/api/group/'.$this->allGroupUsers[0]->group_id.'/user/'.$this->allGroupUsers[0]->id.'/delete');

        $this->assertEquals('Group user is deleted', $response->getData());
        $response->assertStatus(204);
        $this->assertEquals(204, $response->status());

        $groupUser = GroupUser::where('id', $this->allGroupUsers[0]->id)->get();
        
        $groupUser = json_decode(json_encode($groupUser), true);

        $this->assertEquals($this->allGroupUsers[0]->firstname, $groupUser[0]['firstname']);
        $this->assertEquals($this->allGroupUsers[0]->name, $groupUser[0]['name']);
        $this->assertEquals(null, $groupUser[0]['group_id']);
    }

    public function test_group_users_controller_join_group()
    {
        $this->be($this->authenticatedUser());

        //preperation
        $response = $this->postJson('/api/group/'.$this->allGroupUsers[0]->group_id.'/user/'.$this->allGroupUsers[0]->id.'/regenerate_code');
        $response_data = $response->getData();

        $response->assertStatus(201);

        //join group
        $data = [
            'code' => $response_data->code,
        ];
        $response = $this->postJson('/api/join_group', $data);
        $response_data = $response->getData();
        
        $response->assertStatus(201);
        $this->assertEquals(201, $response->status());

        $this->assertEquals($response_data->code, null);
        $this->assertEquals($response_data->user_id, $this->allUsers[0]->id);
    }

    public function test_group_users_controller_regenerate_group_user_code()
    {
        $this->be($this->authenticatedUser());

        $response = $this->postJson('/api/group/'.$this->allGroupUsers[0]->group_id.'/user/'.$this->allGroupUsers[0]->id.'/regenerate_code');
        $response_data = $response->getData();
       
        $response->assertStatus(201);
        $this->assertEquals(201, $response->status());
        $this->groupUserDataTests($this->allGroupUsers[0], $response_data);

        $this->assertNotEquals(null, $response_data->code);
        $this->assertNotEquals($this->allGroupUsers[0]['code'], $response_data->code);
    }    
}