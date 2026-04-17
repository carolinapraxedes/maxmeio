<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contract_items', function (Blueprint $table) {
            $table->id();

            // Aqui o cascade é recomendado: se o contrato for deletado, os itens perdem o sentido.
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
            $table->string('description');
            $table->integer('quantity');            
            $table->decimal('unit_price', 15, 2); 
        

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_items');
    }
};
