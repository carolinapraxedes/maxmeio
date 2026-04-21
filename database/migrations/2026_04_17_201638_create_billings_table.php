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
            $table->foreignId('contract_id')->constrained('contracts');     
            $table->string('status')->default(BillingStatus::PENDING->value);          
            $table->date('due_date'); // data_vencimento 
            $table->decimal('partial_paid', 10, 2)->default(0); // valor_pago (acumulado)      
            $table->text('cancellation_reason')->nullable(); 
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
