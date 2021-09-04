<?php

namespace Database\Factories;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\GroupUser;
use App\Models\Group;

use Illuminate\Database\Eloquent\Factories\Factory;

class GroupUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GroupUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'firstname' => $this->faker->name,
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'group_id' => Group::all()->random()->id,
            'user_id' => null,
            'verified' => 0
        ];
    }
}
