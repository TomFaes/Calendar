<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;

use App\Models\Group;
use App\Repositories\GroupRepo;
use App\Repositories\UserRepo;

class GroupTest extends TestCase
{
    protected $testData;
    protected $data;
    protected $repo;
    protected $allUsers;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();

        $this->repo =  new GroupRepo();
        $this->testData = Group::with(['groupUsers', 'admin'])->get();

        $user = new UserRepo();
        $this->getAllUsers = $user->getAllUsers();

        //default dataset
        $this->data = [
            'name' => 'name',
            'admin_id' => $this->getAllUsers[0]->id,
        ];
    }

    /**
     * Default data test
     */
    protected function dataTests($data, $testData) : void
    {
        $this->assertInstanceOf(Group::class, $testData);
        $this->assertEquals($data['name'], $testData->name);
        $this->assertEquals($data['admin_id'], $testData->admin_id);
    }

    public function test_get_groups()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m Group Repository Test:   [0m';
        
        
        $found = $this->repo->getGroups();
        $this->assertEquals(10, count($found));
        echo PHP_EOL.'[42m OK  [0m get all  Groups';
    }

    public function test_get_group()
    {
        $found = $this->repo->getGroup($this->testData[0]->id);
        $this->dataTests($found, $this->testData[0]);
        echo PHP_EOL.'[42m OK  [0m get group';
    }

    public function test_get_user_groups()
    {
        $found = $this->repo->getUserGroups($this->testData[0]->id);
        $this->assertEquals(10, count($found));
        echo PHP_EOL.'[42m OK  [0m get all groups of a user';
    }

    public function test_create_group()
    {
        $group = $this->repo->create($this->data, $this->getAllUsers[0]->id);
        $this->dataTests($this->data, $group);
        echo PHP_EOL.'[42m OK  [0m create group';
    }

    public function test_update_group()
    {
        $data = [
            'name' => "update name",
            'admin_id' => $this->getAllUsers[0]->id,
        ];

        $group = $this->repo->update($data, $this->testData[0]->id);
        $this->dataTests($data, $group);

        echo PHP_EOL.'[42m OK  [0m update group';
    }

    public function test_delete_group()
    {
        $this->repo->delete($this->testData[0]->id);
        $found = $this->repo->getGroups();
        $this->assertEquals(9, count($found));
        echo PHP_EOL.'[42m OK  [0m delete group';
    }
}