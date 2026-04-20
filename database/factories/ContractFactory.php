<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'date_start' => now()->subMonths(fake()->numberBetween(1, 12)),
            'date_end' => fake()->optional()->dateTimeBetween('now', '+1 year'),
        ];
    }
}
