<?php
namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Absence;
use App\Models\Season;
use App\Models\GroupUser;

class AbsenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Absence::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'season_id' =>  Season::all()->random()->id,
            'date' => Carbon::now()->addMonths(1)->format('Y-m-d'),
            'group_user_id' => GroupUser::all()->random()->id,
        ];
    }
}