<?php

namespace Tests\Unit\Service;

use Tests\TestCase;

use Database\Seeders\GeneratorSeeder;

class GroupTest extends TestCase
{
    protected $groupService;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);        
    }

    public function test_get_user_groups()
    {
        $this->markTestIncomplete(
            "This test hasn't been created"
        );
    }
    

    
}