<?php

namespace Tests\Unit\Service;

use Tests\TestCase;

use App\Models\User;
use App\Services\UserService;
use Database\Seeders\GeneratorSeeder;

class UserTest extends TestCase
{
    protected $testData;
    protected $data;
    protected $userService;
    protected $defaultUser;
    protected $recordCount;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->userService =  new UserService();
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

    public function test_ranomize_user(){
        $testUser = clone $this->testData[0];
        $forgetUser = $this->userService->randomizeUser($this->testData[0]);

        $this->assertInstanceOf(User::class, $forgetUser);
        $this->assertNotEquals($testUser['firstname'], $forgetUser->firstname);
        $this->assertNotEquals($testUser['name'], $forgetUser->name);
        $this->assertNotEquals($testUser['email'], $forgetUser->email);
        $this->assertNotEquals($testUser['password'], $forgetUser->password);
        $this->assertEquals(1, $forgetUser->forget_user);
    }

    public function test_setup_social_user(){
        $socialUser = [
            'given_name' => $this->defaultUser['firstname'],
            'family_name' => $this->defaultUser['name'],
            'email' => $this->defaultUser['email'],
            'role' => $this->defaultUser['role'],
        ];        
        $user = $this->userService->setupSocialUser($socialUser);
        $this->dataTests($this->defaultUser, $user);
    }
}