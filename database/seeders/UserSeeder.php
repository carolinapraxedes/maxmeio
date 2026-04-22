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

        User::create([
            'name' => 'Ana Financeiro',
            'email' => 'financeiro@agencia.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        


        User::create([
            'name' => 'Bruno Gestor OS',
            'email' => 'os@agencia.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        

        
        User::factory(5)->create();
    }
}
