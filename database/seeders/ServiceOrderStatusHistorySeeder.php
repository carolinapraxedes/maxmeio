<?php

namespace Database\Seeders;

use App\Models\ServiceOrder;
use App\Models\ServiceOrderStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceOrderStatusHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $os = ServiceOrder::first();
        $user = User::where('role', 'operator')->first();

        if ($os && $user) {
            
            ServiceOrderStatusHistory::create([
                'service_order_id' => $os->id,
                'user_id' => $user->id,
                'old_status' => null,
                'new_status' => 'pending',
                'changed_at' => $os->created_at,
            ]);

            
            ServiceOrderStatusHistory::create([
                'service_order_id' => $os->id,
                'user_id' => $user->id,
                'old_status' => 'pending',
                'new_status' => 'in_progress',
                'changed_at' => now(),
            ]);
        }

        
        ServiceOrderStatusHistory::factory(10)->create();
    }
}
