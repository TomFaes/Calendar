<?php

use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        // Disable Foreign key check for this connection before running seeders
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        factory(Group::class, 10)->create();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        /*
        Group::factory()
            ->times(10)
            ->create();
        */    
    }
    
}
