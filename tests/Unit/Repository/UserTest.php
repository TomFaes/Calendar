<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;

use App\Models\User;
use App\Repositories\UserRepo;
use Database\Seeders\GeneratorSeeder;

class UserTest extends TestCase
{
    protected $testData;
    protected $data;
    protected $repo;
    protected $defaultUser;
    protected $recordCount;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->repo =  new UserRepo();
        $this->testData = User::all();
        $this->recordCount = count($this->testData);

        $this->defaultUser = [
            'firstname' => "Test first name",
            'name' => "Test name",
            'email' => "test@mail.be",
            'role' => "User",
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
        $found = $this->repo->getAllUsers();
        $this->assertEquals($this->recordCount, count($found));
    }


    public function test_get_user()
    {
        $found = $this->repo->getUser($this->testData[0]->id);

        $this->dataTests($found, $this->testData[0]);
    }

    public function test_check_mail_is_found()
    {
        $found = $this->repo->checkEmail($this->testData[0]->email);
        $this->dataTests($found, $this->testData[0]);
    }

    public function test_check_mail_is_not_found()
    {
        $found = $this->repo->checkEmail('aaaaaa@bbbb@cccccccc');
        $this->assertNull($found);
    }

    public function test_create_user()
    {
        $user = $this->repo->create($this->defaultUser);
        $this->dataTests($this->defaultUser, $user);
    }

    public function test_create_social_user()
    {
        $socialUser = [
            'given_name' => $this->defaultUser['firstname'],
            'family_name' => $this->defaultUser['name'],
            'email' => $this->defaultUser['email'],
            'role' => $this->defaultUser['role'],
        ];

        $user = $this->repo->createSocialUser($socialUser);
        $this->dataTests($this->defaultUser, $user);
    }

    public function test_update_user()
    {
        $user = $this->repo->update($this->defaultUser, $this->testData[0]->id);
        $this->dataTests($this->defaultUser, $user);
    }

    public function test_update_password(){
        $data['password'] = "PHP UNIT TESTING";
        $user = $this->repo->updatePassword($data ,$this->testData[0]->id);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEquals($this->testData[0]['password'], $user->password);
    }
     
    public function test_forget_user(){
        $forgetUser = $this->repo->forgetUser($this->testData[0]->id);

        $this->assertInstanceOf(User::class, $forgetUser);
        $this->assertNotEquals($this->testData[0]['firstname'], $forgetUser->firstname);
        $this->assertNotEquals($this->testData[0]['name'], $forgetUser->name);
        $this->assertNotEquals($this->testData[0]['email'], $forgetUser->email);
        $this->assertNotEquals($this->testData[0]['password'], $forgetUser->password);
        $this->assertEquals(1, $forgetUser->forget_user);
    }

    public function test_delete_user()
    {
        $forgetUser = $this->repo->delete($this->testData[0]->id);
        
        $this->assertInstanceOf(User::class, $forgetUser);
        $this->assertNotEquals($this->testData[0]['firstname'], $forgetUser->firstname);
        $this->assertNotEquals($this->testData[0]['name'], $forgetUser->name);
        $this->assertEquals($this->testData[0]['role'], $forgetUser->role);
        $this->assertNotEquals($this->testData[0]['email'], $forgetUser->email);
        $this->assertNotEquals($this->testData[0]['password'], $forgetUser->password);
        $this->assertEquals(1, $forgetUser->forget_user);
    }
}