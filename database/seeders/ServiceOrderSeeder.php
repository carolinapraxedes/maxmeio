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
        /*$manager = User::where('role', 'os_manager')->first() 
               ?? User::factory()->create(['role' => 'os_manager']);*/

        \App\Models\ServiceOrder::observe(\App\Observers\ServiceOrderObserver::class);
                
        $contract = Contract::first() ?? Contract::factory()->create();
        $user = User::first() ?? User::factory()->create();

        
        ServiceOrder::factory()->create([
            'title' => 'Manutenção de Banco de Dados',
            'contract_id' => $contract->id,
            'user_id'=> $user->id,
            //'user_id' => $manager->id,
            'status' => 'pendente', 
            'actual_hours' => 0,
        ]);

        
        ServiceOrder::factory()->create([
            'title' => 'Desenvolvimento de API',
            'contract_id' => $contract->id,
            'user_id'=> $user->id,
            //'user_id' => $manager->id,
            'status' => 'em_andamento',
            'actual_hours' => 0,
        ]);

        
        ServiceOrder::factory()->create([
            'title' => 'Reunião de Planejamento',
            'contract_id' => $contract->id,
            'user_id'=> $user->id,
            //'user_id' => $manager->id,
            'status' => 'concluida',
            'estimated_hours' => 2.00,
            'actual_hours' => 2.50,
        ]);
        
        
        ServiceOrder::factory()->create([
            'title' => 'Projeto Obsoleto',
            'contract_id' => $contract->id,
            'user_id'=> $user->id,
            //'user_id' => $manager->id,
            'status' => 'cancelada',
        ]);

        
       // ServiceOrder::factory(5)->create();

        #Testes de Transição (Para o Histórico via Observer)
        $os1 = ServiceOrder::factory()->create(['status' => 'pendente']);
        $os1->update(['status' => 'em_andamento']);
        $os1->save();


        $os2 = ServiceOrder::factory()->create(['status' => 'pendente']);
        $os2->update(['status' => 'em_andamento']);
        $os2->update(['status' => 'concluida']);


        $os3 = ServiceOrder::factory()->create(['status' => 'pendente']);
        $os3->update(['status' => 'em_andamento']);
        $os3->update(['status' => 'pausada']);


        $os4 = ServiceOrder::factory()->create(['status' => 'pendente']);
        $os4->update(['status' => 'cancelada']);
    
    }
}
