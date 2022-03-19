<?php

namespace Tests\Unit\Route;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\GeneratorSeeder;
use Laravel\Sanctum\Sanctum;

class UserTest extends TestCase
{
    protected $allUsers;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);
        $this->allUsers = User::all();
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
    protected function ProfileDataTests($data, $testData) : void
    {

        $this->assertEquals($data['firstname'], $testData->firstname);
        $this->assertEquals($data['name'], $testData->name);
        $this->assertEquals($data['firstname']." ".$data['name'], $testData->full_name);
        if(isset($data['role']) === true){
            $this->assertEquals($data['role'], $testData->role);
        }else{
            $this->assertEquals('User', $testData->role);
        }
    }

    public function test_profile_controller_index()
    {
        $this->be($this->authenticatedUser('User'));

        $response = $this->get('/api/profile');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->getData();
        
        $this->ProfileDataTests($this->allUsers[0], $response_data);
    }

    public function test_profile_controller_update()
    {
        $this->be($this->authenticatedUser('User'));
        $data = User::factory()->make()->toArray();

        $response = $this->postJson('/api/profile/', $data);
        $response_data = $response->getData();

        $response->assertStatus(201);
        $this->assertEquals(201, $response->status());
        $this->ProfileDataTests($data, $response_data);
    }

    public function test_ProfileController_destroy()
    {
        $this->be($this->authenticatedUser('User'));

        $response = $this->postJson('/api/profile/delete');
        $response->assertStatus(204);
        $this->assertEquals(204, $response->status());
        $response_data = $response->getData();
        $this->assertEquals('Profile is deleted', $response_data);

        //get the delete user
        $updateUser = User::where('id', $this->allUsers[0]->id)->first();
        $this->assertNotEquals($updateUser->firstname, $this->allUsers[0]->firstname);
        $this->assertNotEquals($updateUser->name, $this->allUsers[0]->name);
        $this->assertEquals($updateUser->role, $this->allUsers[0]->role);
        $this->assertNotEquals($updateUser->email, $this->allUsers[0]->email);
        $this->assertEquals($updateUser->forget_user, 1);
    }
}