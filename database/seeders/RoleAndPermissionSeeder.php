<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'manual credit']); 
        Permission::create(['name' => 'manage service orders']); 

        
        $financial = Role::create(['name' => 'financial']);
        $financial->givePermissionTo('manual credit');
        $financial->givePermissionTo('manage service orders');

        
        $employee = Role::create(['name' => 'os_manager']);
        $employee->givePermissionTo('manage service orders');
    }
}
