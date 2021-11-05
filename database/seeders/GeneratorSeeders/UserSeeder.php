<?php
namespace Database\Seeders\GeneratorSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        User::factory()
            ->count(10)
            ->create();
        
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
