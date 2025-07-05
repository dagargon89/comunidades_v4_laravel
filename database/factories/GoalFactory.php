<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Component;
use App\Models\Goal;
use App\Models\Organization;

class GoalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Goal::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'description' => fake()->text(),
            'number' => fake()->word(),
            'component_id' => Component::factory(),
            'organization_id' => Organization::factory(),
        ];
    }
}
