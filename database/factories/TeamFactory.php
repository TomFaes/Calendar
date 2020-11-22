<?php

use Carbon\Carbon;
use App\Models\GroupUser;
use App\Models\Season;

use App\Models\Team;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Team::class, function (Faker $faker) {
    return [
        'season_id' =>  Season::first()->id,
        'date' => Carbon::now()->addMonths(1)->format('Y-m-d'),
        'team' =>$faker->randomElement(['team1', 'team2', 'team3', 'free']),
        'group_user_id' => GroupUser::inRandomOrder()->first(),
    ];
});
