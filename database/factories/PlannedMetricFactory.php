<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\DataCollector;
use App\Models\DataCollectors,;
use App\Models\PlannedMetric;

class PlannedMetricFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlannedMetric::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'description' => fake()->text(),
            'unit' => fake()->regexify('[A-Za-z0-9]{100}'),
            'year' => fake()->word(),
            'month' => fake()->word(),
            'is_product' => fake()->word(),
            'is_population' => fake()->word(),
            'population_target_value' => fake()->randomFloat(2, 0, 99999999.99),
            'population_real_value' => fake()->randomFloat(2, 0, 99999999.99),
            'product_target_value' => fake()->randomFloat(2, 0, 99999999.99),
            'product_real_value' => fake()->randomFloat(2, 0, 99999999.99),
            'data_collector_id' => DataCollectors,::factory(),
            'activity_id' => Activity::factory(),
            'nullable_id' => DataCollector::factory(),
        ];
    }
}
