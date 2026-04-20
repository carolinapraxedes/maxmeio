<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\ContractItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContractItem>
 */
class ContractItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contract_id' => Contract::factory(), 
            'description' => fake()->randomElement([
                'Desenvolvimento de sistema', 
                'Manutenção de site', 
                'Gestão de tráfego', 
                'Criação de conteúdo'
            ]),
            'quantity' => fake()->numberBetween(1, 5),
            'unit_price' => fake()->randomFloat(2, 100, 2500),
        ];
    }
}
