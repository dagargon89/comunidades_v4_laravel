<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Program;
use App\Models\ProgramIndicator;

class ProgramIndicatorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProgramIndicator::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'initial_value' => fake()->randomFloat(2, 0, 99999999.99),
            'final_value' => fake()->randomFloat(2, 0, 99999999.99),
            'program_id' => Program::factory(),
        ];
    }
}
