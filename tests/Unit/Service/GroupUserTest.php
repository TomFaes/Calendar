<?php

namespace Tests\Unit\Service;

use Tests\TestCase;

use App\Models\GroupUser;
use App\Models\User;
use App\Services\GroupUserService;
use Database\Seeders\GeneratorSeeder;

class GroupUserTest extends TestCase
{
    protected $groupUserService;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->groupUserService = new GroupUserService();
    }

    public function test_create_code()
    {
        $code = $this->groupUserService->createCode();
        //microtime has 40 characters and a random string of 20
        $this->assertEquals(strlen($code), 60);
    }

    public function test_join_group()
    {
        $user = User::first();
        $groupUser = GroupUser::first();
        $code = $this->groupUserService->createCode();
        $groupUser->code = $code;
        $groupUser->save();
        $groupUser = GroupUser::first();

        $this->assertEquals(strlen($groupUser->code), 60);
        $this->assertEquals($groupUser->code, $code);

        $this->groupUserService->joinGroup($code, $user->id);
        $groupUser = GroupUser::first();

        $this->assertEquals($groupUser->user_id, $user->id);
        $this->assertEquals($groupUser->code, null);
    }

    public function test_regenerate_group_user_code()
    {
        $groupUser = GroupUser::first();
        $code = $this->groupUserService->createCode();
        $groupUser->code = $code;
        $groupUser->save();
        $groupUser = GroupUser::first();
        
        $found = $this->groupUserService->regenerateGroupUserCode($groupUser);
        $this->assertNotEquals($found->code, $code);
    }
}