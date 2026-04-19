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
        Schema::create('service_order', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            $table->string('title');
            $table->text('description')->nullable();
            
            
            $table->decimal('estimated_hours', 8, 2);
            $table->decimal('actual_hours', 8, 2)->default(0);
            
            // Status atual da OS
            $table->string('status'); 
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_service_order');
    }
};
