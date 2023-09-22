<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'vacation_balance' => fake()->numberBetween(0, 20),
            'email' => fake()->unique()->safeEmail(),
            // created at fake data between 2019-01-01 and now
            'created_at' => fake()->dateTimeBetween('-3 years', 'now'),

        ];
    }
}
