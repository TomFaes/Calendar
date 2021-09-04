<?php

namespace Tests\Unit\Route;

use Tests\TestCase;
use App\Models\User;
use App\Repositories\UserRepo;
use Laravel\Passport\Passport;


class UserTest extends TestCase
{
    protected $allUsers;
    protected $recordCount;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();
        $repo = new UserRepo();
        $this->allUsers = $repo->getAllUsers();
        $this->recordCount = count($this->allUsers);
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
    protected function dataTests($data, $testData) : void
    {
        $this->assertEquals($data['firstname'], $testData['firstname']);
        $this->assertEquals($data['name'], $testData['name']);
        $this->assertEquals($data['firstname']." ".$data['name'], $testData['fullName']);
        if(isset($data['role']) === true){
            $this->assertEquals($data['role'], $testData['role']);
        }else{
            $this->assertEquals('User', $testData['role']);
        }
    }

    public function test_UserController_index()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m User api routest:   [0m';

        $this->be($this->authenticatedUser());

        $response = $this->get('/api/user');
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());

        $response_data = $response->decodeResponseJson(); 
        $this->assertEquals(count($response_data['data']), $this->recordCount);

        echo PHP_EOL.'[42m OK  [0m test index in the UserController';
    }

    public function test_UserController_store()
    {
        $this->be($this->authenticatedUser());

        $data = [
            'firstname' => 'Firstname',
            'name' => 'Lastname',
            'email' => 'test@test.be',
        ];

        $response = $this->postJson('/api/user', $data);
        $response_data = $response->decodeResponseJson(); 
       
        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->dataTests($data, $response_data);

        echo PHP_EOL.'[42m OK  [0m test store method in the UserController';
    }

    public function test_UserController_update()
    {
        $this->be($this->authenticatedUser());

        $testUser = $this->allUsers[0];

        $data = [
            'firstname' => 'Firstname',
            'name' => 'Lastname',
            'email' => 'test@test.be',
        ];

        $response = $this->postJson('/api/user/'.$testUser->id, $data);
        $response_data = $response->decodeResponseJson(); 
       
        $response->assertStatus(201);
        $this->assertEquals(201, $response->status());
        $this->dataTests($data, $response_data);

        echo PHP_EOL.'[42m OK  [0m test update method in the UserController';
    }

    public function test_UserController_destroy()
    {
        $this->be($this->authenticatedUser('Admin'));

        $testUser = $this->allUsers[0];

        $response = $this->postJson('/api/user/'.$testUser->id.'/delete');
        $response->assertStatus(204);
        $this->assertEquals(204, $response->status());

        echo PHP_EOL.'[42m OK  [0m test destroy method in the UserController';
    }

    public function test_ProfileController_index()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m Profile api routest:   [0m';

        $this->be($this->authenticatedUser('User'));

        $response = $this->get('/api/profile');
        $response_data = $response->decodeResponseJson(); 

        $response->assertStatus(200);
        $this->assertEquals(200, $response->status());
        $this->dataTests($this->allUsers[0], $response_data);

        echo PHP_EOL.'[42m OK  [0m test index in the ProfileController';
    }

    public function test_ProfileController_update()
    {
        $this->be($this->authenticatedUser('User'));

        $data = [
            'firstname' => 'Firstname',
            'name' => 'Lastname',
            'email' => 'test@test.be',
        ];

        $response = $this->postJson('/api/profile/', $data);
        $response_data = $response->decodeResponseJson(); 

        $response->assertStatus(201);
        $this->assertEquals(201, $response->status());
        $this->dataTests($data, $response_data);

        echo PHP_EOL.'[42m OK  [0m test update method in the UserController';
    }

    public function test_ProfileController_destroy()
    {
        $this->be($this->authenticatedUser('User'));

        $response = $this->postJson('/api/profile/delete');
        $response->assertStatus(204);
        $this->assertEquals(204, $response->status());

        //get the delete user
        $response = $this->get('/api/profile');
        $response_data = $response->decodeResponseJson(); 

        $this->assertNotEquals($response_data['firstname'], $this->allUsers[0]->firstname);
        $this->assertNotEquals($response_data['name'], $this->allUsers[0]->name);
        $this->assertEquals($response_data['role'], $this->allUsers[0]->role);
        $this->assertNotEquals($response_data, $this->allUsers[0]->email);

        echo PHP_EOL.'[42m OK  [0m test destroy method in the ProfileController';
    }
    
}