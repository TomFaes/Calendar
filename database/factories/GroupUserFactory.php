<?php

use App\Models\GroupUser;
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

$factory->define(GroupUser::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'firstname' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'group_id' =>  App\Models\Group::first(),
        'user_id' => Null,
        'verified' => 0
    ];
});
