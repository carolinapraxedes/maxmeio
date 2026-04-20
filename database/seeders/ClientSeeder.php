<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::factory(10)->create();
        
        
        Client::factory()->create([
            'name' => 'Cliente Zé Brás',
            'document' => '12345678901',
            'credit_balance' => 1000.00, 
        ]);
    }
}
