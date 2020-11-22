<?php

namespace Tests\Unit\Service;

use Tests\TestCase;

use App\Models\User;
use App\Services\GroupService;

class GroupTest extends TestCase
{
    protected $repo;
    protected $groupService;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();

        $this->allUsers = User::all();
        $this->groupService = resolve(GroupService::class);
    
        //default dataset
        $this->data = [
            'firstname' => 'Firstname',
        ];
    }

    public function test_checkExistingUser()
    {
        echo "\n\n--------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m group service Test:   [0m';

        $check_email = $this->groupService->checkExistingUser($this->allUsers[0]->email);
        $this->assertTrue($check_email);

        $check_email = $this->groupService->checkExistingUser('testqsdfqdsfffffsdfqqsdf@test.be');
        $this->assertFalse($check_email);        
        
        echo PHP_EOL.'[42m OK  [0m test checkNewUser service';
    }
}