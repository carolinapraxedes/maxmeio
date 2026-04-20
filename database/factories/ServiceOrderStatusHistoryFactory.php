<?php

namespace Database\Factories;

use App\Models\ServiceOrder;
use App\Models\ServiceOrderStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceOrderStatusHistory>
 */
class ServiceOrderStatusHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_order_id' => ServiceOrder::inRandomOrder()->first()?->id ?? ServiceOrder::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'old_status' => fake()->randomElement(['aberta', 'em_andamento', 'pausada', 'concluida', 'cancelada',null]),
            'new_status' => fake()->randomElement(['aberta', 'em_andamento', 'pausada', 'concluida', 'cancelada']),
            'changed_at' => now(),
        ];
    }
}
