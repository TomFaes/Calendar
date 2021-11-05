<?php

namespace Tests\Unit\Repository;

use App\Models\Group;
use Tests\TestCase;

use App\Models\GroupUser;
use App\Models\User;
use App\Repositories\GroupUserRepo;
use Database\Seeders\GeneratorSeeder;

class GroupUserTest extends TestCase
{
    protected $testData;
    protected $recordCount;
    protected $data;
    protected $repo;

    protected $allUsers;
    protected $allGroups;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->repo =  new GroupUserRepo();
        $this->testData = GroupUser::with(['group', 'user'])->get();
        $this->recordCount = count($this->testData);

        $this->allUsers = User::all();

        $this->allGroups = Group::all();

        //default dataset
        $this->data = [
            'firstname' => 'Firstname',
            'name' => 'Lastname',
            'group_id' => $this->allGroups[0]->id,
            'user_id' => $this->allUsers[0]->id,
        ];
    }

    /**
     * Default data test
     */
    protected function dataTests($data, $testData) : void
    {
        $this->assertInstanceOf(GroupUser::class, $testData);
        $this->assertEquals($data['firstname'], $testData->firstname);
        $this->assertEquals($data['name'], $testData->name);
        $this->assertEquals($data['group_id'], $testData->group_id);
        $this->assertEquals($data['user_id'], $testData->user_id);
    }

    public function test_get_all_group_users()
    {      
        $found = $this->repo->getAllGroupUsers();
        $this->assertEquals($this->recordCount, count($found));
    }

    public function test_get_group_user()
    {        
        $found = $this->repo->getGroupUser($this->testData[0]->id);
        $this->dataTests($found, $this->testData[0]);
    }

    public function test_get_all_users_of_a_group()
    {
        $usersOfGroup = GroupUser::with(['group', 'user'])->where('group_id', $this->allGroups[0]->id)->get();

        $found = $this->repo->getUsersOfGroup($this->allGroups[0]->id);

        $this->assertEquals(count($usersOfGroup), count($found));
    }

    public function test_get_all_groups_of_a_user()
    {
        $countUserGroups = GroupUser::with(['group', 'user'])->where('user_id', $this->allUsers[0]->id)->get();

        $found = $this->repo->getGroupsOfUser($this->allUsers[0]->id);
        $this->assertEquals(count($countUserGroups), count($found));
    }

    public function test_create_group_user()
    {
        $groupUser = $this->repo->create($this->data);
        $this->dataTests($this->data, $groupUser);
    }

    public function test_update_group_user()
    {
        $groupUser = $this->repo->update($this->data, $this->testData[0]->id);
        $this->dataTests($this->data, $groupUser);
    }

    public function test_delete_group_user()
    {
        $delete = $this->repo->delete($this->testData[0]->id);
        $this->assertTrue($delete);

        $found = $this->repo->getGroupUser($this->testData[0]->id);
        $this->assertInstanceOf(GroupUser::class, $found);
        $this->assertEquals($found->group_id, null);
    }

    public function test_create_code_group_user(){
        $code = $this->repo->createCode();
        $this->assertEquals(strlen ($code), 60);
    }

    public function test_join_group(){
        //create user with code
        $data = [
            'firstname' => 'Jane',
            'name' => 'Doe',
            'email' => 'test@test.be',
            'group_id' => $this->testData[0]->group_id,
        ];
        $newUser = $this->repo->create($data);

        $user = User::all()->random(1)->first();
        $testUser = $this->repo->joinGroup($newUser->code, $user->id);

        $this->assertEquals($testUser->code, null);
        $this->assertEquals($testUser->user_id, $user->id);
    }

    public function test_regenerate_code_for_non_user(){
         //create user with code
         $data = [
            'firstname' => 'Jane',
            'name' => 'Doe',
            'email' => 'test@test.be',
            'group_id' => $this->testData[0]->group_id,
        ];
        $newUser = $this->repo->create($data);

        $regenerateCode = $this->repo->regenerateGroupUserCode($newUser->id);

        $this->assertNotEquals($regenerateCode->code,$newUser->code);
    }

    public function test_regenerate_code_with_user_id(){
        //create user with code
        $data = [
           'firstname' => 'Jane',
           'name' => 'Doe',
           'email' => 'test@test.be',
           'group_id' => $this->testData[0]->group_id,
       ];
       $newUser = $this->repo->create($data);

       //join group
       $user = User::all()->random(1)->first();
       $newUser = $this->repo->joinGroup($newUser->code, $user->id);

       $regenerateCode = $this->repo->regenerateGroupUserCode($newUser->id);

       $this->assertFalse($regenerateCode);
    }
}