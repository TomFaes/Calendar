<?php

namespace Database\Factories;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\GroupUser;
use App\Models\Season;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'season_id' =>  Season::first()->id,
            'date' => Carbon::now()->addMonths(1)->format('Y-m-d'),
            'team' =>$this->faker->randomElement(['team1', 'team2', 'team3', 'free']),
            'group_user_id' => GroupUser::inRandomOrder()->first(),
        ];
    }
}
