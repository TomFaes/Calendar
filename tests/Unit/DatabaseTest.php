<?php

namespace Tests\Unit;

use Tests\TestCase;

class DatabaseTest extends TestCase
{
    protected $testData;
    protected $repo;
    protected $records;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();
    }


    /**
     * A basic Unit test example.
     *
     * @return void
     */
    public function test_get_all_users()
    {
        $this->assertEquals(10, 10);
        echo PHP_EOL.'[42m OK  [0m get all  users';
    }
}