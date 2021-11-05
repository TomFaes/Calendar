<?php
namespace Database\Seeders\GeneratorSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Season;

class SeasonSeeder extends Seeder
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

        Season::factory()
            ->count(5)
            ->create();
        //factory(Season::class, 10)->create();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    
}
