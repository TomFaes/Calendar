<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Season;

use App\Models\Absence;
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

$factory->define(Absence::class, function (Faker $faker) {
    return [
        'season_id' =>  Season::first()->id,
        'date' => Carbon::now()->addMonths(1)->format('Y-m-d'),
        'user_id' => User::first(),
    ];
});
