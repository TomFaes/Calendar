<?php
namespace Database\Seeders\GeneratorSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Absence;

class AbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Eloquent::unguard();

        // Disable Foreign key check for this connection before running seeders
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Absence::factory()
            ->count(10)
            ->create();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    
}
