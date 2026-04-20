<?php

namespace Database\Factories;

use App\Enums\ServiceOrderStatus;
use App\Models\Contract;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceOrder>
 */
class ServiceOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            
            'contract_id' => Contract::inRandomOrder()->first()?->id ?? Contract::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            //'user_id' => User::where('role', 'os_manager')->inRandomOrder()->first()?->id ?? User::factory(),
            
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'estimated_hours' => fake()->randomFloat(2, 1, 40), 
            'actual_hours' => 0, 
            'status' => fake()->randomElement(ServiceOrderStatus::cases())->value,
        ];
    }
}
