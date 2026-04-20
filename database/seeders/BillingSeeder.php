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
        $contract = Contract::first();

        if (!$contract) {
            return;
        }

        // 1. Billig paid
        Billing::factory()->create([
            'contract_id' => $contract->id,
            'status' => BillingStatus::PAID->value,
            'total_amount' => 1500.00,
            'paid_amount' => 1500.00,
            'due_date' => now()->subDays(10),
        ]);

        // 2. Billing partial paid
        Billing::factory()->create([
            'contract_id' => $contract->id,
            'status' => BillingStatus::PARTIAL_PAID->value,
            'total_amount' => 1000.00,
            'paid_amount' => 400.00,
            'due_date' => now()->subDays(5),
        ]);

        // 3. Billing overdue 
        Billing::factory()->create([
            'contract_id' => $contract->id,
            'status' => BillingStatus::OVERDUE->value,
            'total_amount' => 2000.00,
            'paid_amount' => 0,
            'due_date' => now()->subMonths(1),
        ]);

        // 4. Billing canceled
        Billing::factory()->create([
            'contract_id' => $contract->id,
            'status' => BillingStatus::CANCELLED->value,
            'total_amount' => 500.00,
            'paid_amount' => 0,
            'cancellation_reason' => 'Contrato rescindido pelo cliente antes do faturamento.',
            'due_date' => now()->addDays(15),
        ]);

        // 5. Random Billings
        Billing::factory(5)->create([
            'status' => BillingStatus::PENDING->value
        ]);
    }
}
