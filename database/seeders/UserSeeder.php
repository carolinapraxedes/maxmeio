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
// 1. Criar Utilizador Administrador (Acesso Total)
        $admin = User::create([
            'name' => 'Admin Sistema',
            'email' => 'admin@agencia.com',
            'password' => Hash::make('password'), 
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');


        $financial = User::create([
            'name' => 'Ana Financeiro',
            'email' => 'financeiro@agencia.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $financial->assignRole('financial');


        $manager = User::create([
            'name' => 'Bruno Gestor OS',
            'email' => 'os@agencia.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('os_manager');

        
        User::factory(5)->create();
    }
}
