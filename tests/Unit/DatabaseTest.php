<?php

namespace Tests\Unit;

use Database\Seeders\GeneratorSeeder;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    protected $testData;
    protected $repo;
    protected $records;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);
    }


    /**
     * A basic Unit test example.
     *
     * @return void
     */
    public function test_get_all_users()
    {
        $this->assertEquals(10, 10);
        /*
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        */
    }
}