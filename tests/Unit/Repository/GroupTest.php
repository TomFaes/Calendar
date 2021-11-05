<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;

use App\Models\Group;
use App\Models\User;
use App\Repositories\GroupRepo;
use Database\Seeders\GeneratorSeeder;

class GroupTest extends TestCase
{
    protected $testData;
    protected $defaultGroup;
    protected $repo;
    protected $allUsers;
    protected $recordCount;


    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->repo =  new GroupRepo();
        $this->testData = Group::with(['groupUsers', 'admin'])->get();
        $this->recordCount = count($this->testData);
        
        $this->allUsers = User::all();

        //default dataset
        $this->defaultGroup = [
            'name' => 'Group name',
            'admin_id' => $this->allUsers[0]->id,
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
        $found = $this->repo->getGroups();
        $this->assertEquals($this->recordCount, count($found));
    }

    public function test_get_group()
    {
        $found = $this->repo->getGroup($this->testData[0]->id);
        $this->dataTests($found, $this->testData[0]);
    }

    public function test_get_user_groups()
    {
        $userId = $this->allUsers[0]->id;
        $userGroups = Group::whereHas('groupUsers', function ($query) use ($userId) {
            $query->where('user_id', '=', $userId);
        })->orWhere('admin_id', $userId)->with(['groupUsers','admin'])->get();

        $found = $this->repo->getUserGroups($this->allUsers[0]->id);
        $this->assertEquals(count($userGroups) , count($found));
    }

    public function test_create_group()
    {
        $group = $this->repo->create($this->defaultGroup, $this->allUsers[0]->id);
        $this->dataTests($this->defaultGroup, $group);
    }

    public function test_update_group()
    {
        $group = $this->repo->update($this->defaultGroup, $this->testData[0]->id);
        $this->dataTests($this->defaultGroup, $group); 
    }

    public function test_delete_group()
    {
        $this->repo->delete($this->testData[0]->id);
        $found = $this->repo->getGroups();
        $this->assertEquals(($this->recordCount - 1), count($found));
    }
}