<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\ActivityFile;

class ActivityFileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivityFile::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'month' => fake()->regexify('[A-Za-z0-9]{20}'),
            'type' => fake()->regexify('[A-Za-z0-9]{100}'),
            'file_path' => fake()->word(),
            'created_at' => fake()->dateTime(),
            'activity_id' => Activity::factory(),
        ];
    }
}
