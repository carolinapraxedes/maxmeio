<?php

namespace Database\Seeders;

use App\Enums\BillingStatus;
use App\Models\Billing;
use App\Models\Contract;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contracts = Contract::inRandomOrder()->take(5)->get();

        if ($contracts->count() < 5) {
            $this->command->error('Precisa de pelo menos 5 contratos para rodar os cenários de teste.');
            return;
        }

        // ID 1: PAID
        Billing::factory()->create([
            'contract_id' => $contracts[0]->id,
            'status' => BillingStatus::PAID,
            'partial_paid' => $contracts[0]->total_value,
            'due_date' => now()->subDays(10),
        ]);

        // ID 2: PARTIAL PAID
        Billing::factory()->create([
            'contract_id' => $contracts[1]->id,
            'status' => BillingStatus::PARTIAL_PAID,
            'partial_paid' => round($contracts[1]->total_value / 2, 2),
            'due_date' => now()->subDays(5),
        ]);

        // ID 3: OVERDUE (Vencido)
        Billing::factory()->create([
            'contract_id' => $contracts[2]->id,
            'status' => BillingStatus::OVERDUE,
            'partial_paid' => 0,
            'due_date' => now()->subMonths(1),
        ]);

        // ID 4: CANCELLED
        Billing::factory()->create([
            'contract_id' => $contracts[3]->id,
            'status' => BillingStatus::CANCELLED,
            'partial_paid' => 0,
            'cancellation_reason' => 'Cliente solicitou encerramento por motivos financeiros.',
            'due_date' => now()->addDays(10),
        ]);

        // ID 5: PENDING
        Billing::factory()->create([
            'contract_id' => $contracts[4]->id,
            'status' => BillingStatus::PENDING,
            'partial_paid' => 0,
            'due_date' => now()->addDays(20),
        ]);

        // Opcional: Gerar mais dados aleatórios para encher a paginação
        Billing::factory(10)->create([
            'contract_id' => fn() => Contract::inRandomOrder()->first()->id,
            'status' => BillingStatus::PENDING,
            'partial_paid' => 0,
        ]);
    }
}
