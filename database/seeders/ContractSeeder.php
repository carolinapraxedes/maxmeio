<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contract;
use App\Models\ContractItem;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $client = Client::first() ?? Client::factory()->create();

        // 1. Active contract
        Contract::factory()
            ->has(ContractItem::factory()->count(2), 'items')
            ->create([
                'client_id' => $client->id,
                'date_start' => now()->subMonths(3),
                'date_end' => null,
            ]);

        // 2. Contract closed
        Contract::factory()
            ->has(ContractItem::factory()->count(1), 'items')
            ->create([
                'client_id' => $client->id,
                'date_start' => now()->subYear(),
                'date_end' => now()->subMonths(2),
            ]);

        // 3. Contract NO items
        Contract::factory()->create([
            'client_id' => $client->id,
            'date_start' => now(),
            'date_end' => null,
        ]);

        // 4. Contract with items
        Contract::factory()
            ->has(ContractItem::factory()->count(10), 'items')
            ->create([
                'client_id' => $client->id,
            ]);

            Contract::factory(5)
            ->make()
            ->each(function ($contract) {
                // Atribui a um cliente aleatório já existente
                $contract->client_id = Client::inRandomOrder()->first()->id;
                $contract->save();

                // Cria de 1 a 4 itens para este contrato específico
                ContractItem::factory(rand(1, 4))->create([
                    'contract_id' => $contract->id
                ]);
            });
    }
    
}
