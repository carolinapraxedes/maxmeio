<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuário Administrador
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@agencia.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // Usuário Financeiro (Crítico para o teste)
        User::factory()->create([
            'name' => 'Finance User',
            'email' => 'finance@agencia.com',
            'role' => 'finance',
            'password' => Hash::make('password'),
        ]);

        // Usuário Operador (Quem faz as OS)
        User::factory()->create([
            'name' => 'Operator User',
            'email' => 'operator@agencia.com',
            'role' => 'operator',
            'password' => Hash::make('password'),
        ]);

        
        User::factory(5)->create();
    }
}
