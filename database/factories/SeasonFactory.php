<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\Season;
use App\Models\User;
use App\Models\Group;

class SeasonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Season::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'begin' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'end' =>  Carbon::now()->addMonths(7)->format('Y-m-d'),
            'public' => 0,
            'is_generated' => 0,
            'active' => 1,
            'group_id' =>  Group::all()->first()->id,
            'admin_id' => User::all()->first()->id,
            'day' => 'do',
            'start_hour' => "20:00",
            'type' => 'TwoFieldTwoHourThreeTeams',
            'public' => 0,
            'allow_replacement' => 0,
        ];
    }
}