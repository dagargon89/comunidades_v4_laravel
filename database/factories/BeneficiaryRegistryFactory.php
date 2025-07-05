<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\BeneficiaryRegistry;
use App\Models\DataCollector;
use App\Models\Location;

class BeneficiaryRegistryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BeneficiaryRegistry::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'last_name' => fake()->lastName(),
            'mother_last_name' => fake()->regexify('[A-Za-z0-9]{100}'),
            'first_names' => fake()->regexify('[A-Za-z0-9]{100}'),
            'birth_year' => fake()->regexify('[A-Za-z0-9]{4}'),
            'gender' => fake()->randomElement(["['M'","'F'","'Male'","'Female']",""]),
            'phone' => fake()->phoneNumber(),
            'signature' => fake()->word(),
            'address_backup' => fake()->word(),
            'activity_id' => Activity::factory(),
            'location_id' => Location::factory(),
            'nullable_id' => DataCollector::factory(),
        ];
    }
}
