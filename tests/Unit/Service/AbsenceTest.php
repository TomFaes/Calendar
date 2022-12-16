<?php

namespace Tests\Unit\Service;

use Tests\TestCase;
use Database\Seeders\GeneratorSeeder;

class AbsenceTest extends TestCase
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
    }

    public function test_get_season_absence_as_an_array_by_group_user()
    {
        $this->markTestIncomplete(
            "This test hasn't been created"
        );
    }
}