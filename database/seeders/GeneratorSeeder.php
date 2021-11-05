<?php

namespace Database\Seeders;

use Database\Seeders\GeneratorSeeders\AbsenceSeeder;
use Database\Seeders\GeneratorSeeders\GroupSeeder;
use Database\Seeders\GeneratorSeeders\GroupUserSeeder;
use Database\Seeders\GeneratorSeeders\SeasonSeeder;
use Database\Seeders\GeneratorSeeders\UserSeeder;
use Illuminate\Database\Seeder;

class GeneratorSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(GroupSeeder::class);
        $this->call(GroupUserSeeder::class);
        $this->call(SeasonSeeder::class);
        $this->call(AbsenceSeeder::class);
    }
}
