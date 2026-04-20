<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::where('role', 'os_manager')->first() 
               ?? User::factory()->create(['role' => 'os_manager']);
                
        $contract = Contract::first() ?? Contract::factory()->create();

        
        ServiceOrder::factory()->create([
            'title' => 'Manutenção de Banco de Dados',
            'contract_id' => $contract->id,
            'user_id' => $manager->id,
            'status' => 'Pendente', 
            'actual_hours' => 0,
        ]);

        
        ServiceOrder::factory()->create([
            'title' => 'Desenvolvimento de API',
            'contract_id' => $contract->id,
            'user_id' => $manager->id,
            'status' => 'em_andamento',
            'actual_hours' => 0,
        ]);

        
        ServiceOrder::factory()->create([
            'title' => 'Reunião de Planejamento',
            'contract_id' => $contract->id,
            'user_id' => $manager->id,
            'status' => 'concluida',
            'estimated_hours' => 2.00,
            'actual_hours' => 2.50,
        ]);
        
        
        ServiceOrder::factory()->create([
            'title' => 'Projeto Obsoleto',
            'contract_id' => $contract->id,
            'user_id' => $manager->id,
            'status' => 'cancelada',
        ]);

        
        ServiceOrder::factory(5)->create();
    
    }
}
