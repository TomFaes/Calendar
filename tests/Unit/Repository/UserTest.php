<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;

use App\Models\User;
use App\Repositories\UserRepo;


class UserTest extends TestCase
{
    protected $testData;
    protected $data;
    protected $repo;
    protected $countGroupUsers;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();
        $this->repo =  new UserRepo();
        $this->testData = $this->repo->getAllUsers();
        $this->recordCount = count($this->testData);

        
     
        //default dataset
        $this->data = [
            'firstname' => 'Firstname',
            'name' => 'Lastname',
            'email' => 'test@test.be',
            'role' => 'User',
        ];
    }

    /**
     * Default data test
     */
    protected function dataTests($data, $testData) : void
    {
        $this->assertInstanceOf(User::class, $testData);
        $this->assertEquals($data['firstname'], $testData->firstname);
        $this->assertEquals($data['name'], $testData->name);
        $this->assertEquals($data['role'], $testData->role);
        $this->assertEquals($data['email'], $testData->email);
    }

    public function test_get_all_users()
    {
        echo "\n\n--------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m User Repository Test:   [0m';
      
        $found = $this->repo->getAllUsers();
        $this->assertEquals($this->recordCount, count($found));
        echo PHP_EOL.'[42m OK  [0m get all  users';
    }

    public function test_get_user()
    {
        $found = $this->repo->getUser($this->testData[0]->id);

        $this->dataTests($found, $this->testData[0]);
        echo PHP_EOL.'[42m OK  [0m get user';
    }

    public function test_check_mail()
    {
        $found = $this->repo->checkEmail($this->testData[0]->email);
        $this->dataTests($found, $this->testData[0]);
        echo PHP_EOL.'[42m OK  [0m get user based on email';
    }

    public function test_create_user()
    {
        $data = [
            'firstname' => "Test first name",
            'name' => "Test name",
            'email' => "test@mail.be",
            'role' => "User",
        ];

        $user = $this->repo->create($data);
        $this->dataTests($data, $user);
        echo PHP_EOL.'[42m OK  [0m create user';
    }

    public function test_create_social_user()
    {
        $socialUser = [
            'given_name' => "Test first name",
            'family_name' => "Test name",
            'email' => "test@mail.be",
            'role' => "User"
        ];
        
        $data = [
            'firstname' => $socialUser['given_name'],
            'name' => $socialUser['family_name'],
            'email' => $socialUser['email'],
            'role' => $socialUser['role'],
        ];

        $user = $this->repo->createSocialUser($socialUser);
        $this->dataTests($data, $user);
        echo PHP_EOL.'[42m OK  [0m create user';
    }
  
    public function test_update_user()
    {
        $data = [
            'firstname' => "Test first name",
            'name' => "Test name",
            'email' => "test@mail.be",
            'role' => "Admin",
        ];

        $user = $this->repo->update($data, $this->testData[0]->id);
        $this->dataTests($data, $user);

        echo PHP_EOL.'[42m OK  [0m update user';
    }
    
    public function test_forget_user(){
        $forgetUser = $this->repo->forgetUser($this->testData[0]->id);
        $this->assertInstanceOf(User::class, $forgetUser);

        $this->assertNotEquals($this->testData[0]['firstname'], $forgetUser->firstname);
        $this->assertNotEquals($this->testData[0]['name'], $forgetUser->name);
        $this->assertEquals($this->testData[0]['role'], $forgetUser->role);
        $this->assertNotEquals($this->testData[0]['email'], $forgetUser->email);
        echo PHP_EOL.'[42m OK  [0m forget user test';
    }

    public function test_update_password(){
        $data['password'] = "PHP UNIT TESTING";
        
        $user = $this->repo->updatePassword($data ,$this->testData[0]->id);
        $password = bcrypt($data['password']);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEquals($this->testData[0]['password'], $user->password);

        echo PHP_EOL.'[42m OK  [0m update password test';
    }

    public function test_delete_user()
    {
        $forgetUser = $this->repo->forgetUser($this->testData[0]->id);
        $this->assertInstanceOf(User::class, $forgetUser);

        $this->assertNotEquals($this->testData[0]['firstname'], $forgetUser->firstname);
        $this->assertNotEquals($this->testData[0]['name'], $forgetUser->name);
        $this->assertEquals($this->testData[0]['role'], $forgetUser->role);
        $this->assertNotEquals($this->testData[0]['email'], $forgetUser->email);
        echo PHP_EOL.'[42m OK  [0m delete user test';
    }
}