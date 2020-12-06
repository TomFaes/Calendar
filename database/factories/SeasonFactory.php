<?php

use Carbon\Carbon;
use App\Models\Season;
use Illuminate\Support\Str;
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

$factory->define(Season::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'begin' => Carbon::now()->subDays(1)->format('Y-m-d'),
        'end' =>  Carbon::now()->addMonths(1)->format('Y-m-d'),
        'active' => 1,
        'group_id' =>  App\Models\Group::first(),
        'admin_id' => App\Models\User::first(),
        'day' => 'do',
        'start_hour' => "20:00",
        'type' => 'TwoFieldTwoHourThreeTeams',
        'public' => 0,
        'allow_replacement' => 0,

    ];
});
