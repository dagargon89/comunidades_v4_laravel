<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\Goal;
use App\Models\Responsible;
use App\Models\SpecificObjective;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'description' => fake()->text(),
            'specific_objective_id' => SpecificObjective::factory(),
            'responsible_id' => Responsible::factory(),
            'goal_id' => Goal::factory(),
        ];
    }
}
