<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;

use App\Models\GroupUser;
use App\Repositories\GroupUserRepo;

use App\Repositories\UserRepo;
use App\Repositories\GroupRepo;


class GroupUserTest extends TestCase
{
    protected $testData;
    protected $data;
    protected $repo;

    protected $allUsers;
    protected $getAllGroups;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();
        $this->repo =  new GroupUserRepo();
        $this->testData = $this->repo->getAllGroupUsers();

        $user = new UserRepo();
        $this->allUsers = $user->getAllUsers();

        $user = new GroupRepo();
        $this->getAllGroups = $user->getGroups();

        //default dataset
        $this->data = [
            'firstname' => 'Firstname',
            'name' => 'Lastname',
            'email' => $this->allUsers[0]->email,
            'group_id' => $this->getAllGroups[0]->id,
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
        $this->assertEquals($data['email'], $testData->email);
        $this->assertEquals($data['group_id'], $testData->group_id);
        $this->assertEquals($data['user_id'], $testData->user_id);
    }

    public function test_get_all_group_users()
    {
        echo "\n\n---------------------------------------------------------------------------------";
        echo PHP_EOL.PHP_EOL.'[44m GroupUser Repository Test:   [0m';
      
        $found = $this->repo->getAllGroupUsers();
        $this->assertEquals(10, count($found));
        echo PHP_EOL.'[42m OK  [0m get all  group users';
    }

    public function test_get_group_user()
    {        
        $found = $this->repo->getGroupUser($this->testData[0]->id);
        $this->dataTests($found, $this->testData[0]);
        echo PHP_EOL.'[42m OK  [0m get group user';
    }

    public function test_get_all_users_of_a_group_user()
    {
        $found = $this->repo->getUsersOfGroup($this->testData[0]->group_id);
        $this->assertEquals(10, count($found));
        echo PHP_EOL.'[42m OK  [0m get all  users of a  group';
    }

    public function test_get_all_groups_of_a_user()
    {
        $found = $this->repo->getGroupsOfUser($this->allUsers[0]->id);
        $this->assertEquals(0, count($found));

        //set user
        $data['user_id'] = $this->allUsers[0]->id;
        //update a groupuser to match a user and verify it
        $this->repo->update($data, $this->testData[0]->id);
        $this->repo->verifyUser($this->testData[0]->id);
        //update a groupuser to match a user and verify it
        $this->repo->update($data, $this->testData[1]->id);
        $this->repo->verifyUser($this->testData[1]->id);
        //update a groupuser to match a user and verify it
        $this->repo->update($data, $this->testData[2]->id);
        $this->repo->verifyUser($this->testData[2]->id);

        $found = $this->repo->getGroupsOfUser($this->allUsers[0]->id);
        $this->assertEquals(3, count($found));
        
        echo PHP_EOL.'[42m OK  [0m get all  groups of a user';
    }

    public function test_get_unverified_group_users()
    {
        $found = $this->repo->getUnverifiedGroupUsers($this->allUsers[0]->id);
        $this->assertEquals(0, count($found));

        //set user
        $data['user_id'] = $this->allUsers[0]->id;
        //update a groupuser to match a user
        $this->repo->update($data, $this->testData[0]->id);
        $this->repo->update($data, $this->testData[1]->id);
        $this->repo->update($data, $this->testData[2]->id);

        $found = $this->repo->getUnverifiedGroupUsers($this->allUsers[0]->id);
        $this->assertEquals(3, count($found));
        
        echo PHP_EOL.'[42m OK  [0m get all  groups where the given user is not verified';
    }

    public function test_get_groups_based_on_email()
    {
        //set data
        $data['email'] = "test@test.be";
        //update a groupuser to match the email
        $this->repo->update($data, $this->testData[0]->id);
        $this->repo->update($data, $this->testData[1]->id);
        $this->repo->update($data, $this->testData[2]->id);

        $found = $this->repo->getGroupsBasedOnEmail($data['email']);
        $this->assertEquals(3, count($found));
        
        echo PHP_EOL.'[42m OK  [0m get all  groups user based on the email who are not verified or have a user id';
    }

    public function test_create_group_user()
    {
        $data = [
            'firstname' => 'Firstname',
            'name' => 'Lastname',
            'email' => 'test@test.be',
            'group_id' => $this->getAllGroups[0]->id,
            'user_id' => null,
        ];

        $groupUser = $this->repo->create($data);
        $this->dataTests($data, $groupUser);
        echo PHP_EOL.'[42m OK  [0m create group User';
    }

    public function test_update_group_user()
    {
        $data = [
            'firstname' => 'Test',
            'name' => 'TEst',
            'email' => 'test@test.be',
            'group_id' => $this->getAllGroups[0]->id,
            'user_id' => null,
        ];

        $groupUser = $this->repo->update($data, $this->testData[0]->id);
        $this->dataTests($data, $groupUser);
        echo PHP_EOL.'[42m OK  [0m update group User';
    }

    public function test_delete_group_user()
    {
        $this->repo->delete($this->testData[0]->id);
        $found = $this->repo->getGroupUser($this->testData[0]->id);
        $this->assertInstanceOf(GroupUser::class, $found);
        $this->assertEquals($found->group_id, null);
        echo PHP_EOL.'[42m OK  [0m delete group User';
    }

    public function test_verify_user()
    {
        $found = $this->repo->getGroupsOfUser($this->allUsers[0]->id);
        $this->assertEquals(0, count($found));

        //set user
        $data['user_id'] = $this->allUsers[0]->id;
        //update a groupuser to match a user and verify it
        $this->repo->update($data, $this->testData[0]->id);
         $found = $this->repo->verifyUser($this->testData[0]->id);

        $this->assertInstanceOf(GroupUser::class, $found);
        $this->assertEquals($found->verified, 1);
        
        echo PHP_EOL.'[42m OK  [0m verify a group user';
    }

    public function test_unverify_user()
    {
        $found = $this->repo->getGroupsOfUser($this->allUsers[0]->id);
        $this->assertEquals(0, count($found));

        //set user
        $data['user_id'] = $this->allUsers[0]->id;
        //update a groupuser to match a user and verify it
        $this->repo->update($data, $this->testData[0]->id);
         $found = $this->repo->verifyUser($this->testData[0]->id);

        $this->assertInstanceOf(GroupUser::class, $found);
        $this->assertEquals($found->verified, 1);

        $found = $this->repo->unverifyUser($this->testData[0]->id);
        $this->assertInstanceOf(GroupUser::class, $found);
        $this->assertEquals($found->user_id, null);
        $this->assertEquals($found->email, null);
        $this->assertEquals($found->verified, 0);
        
        echo PHP_EOL.'[42m OK  [0m unverify a group user';
    }
}