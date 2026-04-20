<?php

namespace Database\Factories;

use App\Enums\BillingStatus;
use App\Models\Billing;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Billing>
 */
class BillingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contract = Contract::inRandomOrder()->first() ?? Contract::factory();

        return [
            'contract_id' => $contract->id,
            'status' => BillingStatus::PENDING->value,
            'due_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            
            'total_amount' => $contract->total_value, 
            'paid_amount' => 0,
            'cancellation_reason' => null,
        ];
    }
}
