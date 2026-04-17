<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\BillingStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts'); // Sem cascade para manter histórico financeiro        
            // Status obrigatórios: pendente, aguardando_pagamento, pago, pago_parcial, inadimplente, negociando, cancelado
            $table->string('status')->default(BillingStatus::PENDING->value);          
            $table->date('due_date'); // data_vencimento 
            $table->decimal('total_amount', 15, 2); // Valor original da cobrança
            $table->decimal('paid_amount', 15, 2)->default(0); // valor_pago (acumulado) [cite: 36]           
            $table->text('cancellation_reason')->nullable(); // motivo obrigatório para cancelado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
