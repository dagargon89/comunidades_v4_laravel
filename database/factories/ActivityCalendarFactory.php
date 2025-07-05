<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\ActivityCalendar;
use App\Models\DataCollector;
use App\Models\Location;

class ActivityCalendarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivityCalendar::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'start_date' => fake()->word(),
            'end_date' => fake()->word(),
            'start_hour' => fake()->word(),
            'end_hour' => fake()->word(),
            'address_backup' => fake()->word(),
            'cancelled' => fake()->word(),
            'change_reason' => fake()->word(),
            'activity_id' => Activity::factory(),
            'location_id' => Location::factory(),
            'data_collector_id' => DataCollector::factory(),
        ];
    }
}
