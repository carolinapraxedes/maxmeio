<?php

namespace App\Observers;

use App\Models\ServiceOrder;
use App\Models\ServiceOrderStatusHistory;

class ServiceOrderObserver
{
    /**
     * Handle the ServiceOrder "created" event.
     */
    public function created(ServiceOrder $serviceOrder): void
    {
        // Registra o "nascimento" da OS no histórico
        ServiceOrderStatusHistory::create([
            'service_order_id' => $serviceOrder->id,
            'user_id'          => $serviceOrder->user_id, 
            'old_status'       => null,
            'new_status'       => $serviceOrder->status,
            'changed_at'       => now(),
        ]);
    }

    /**
     * Handle the ServiceOrder "updated" event.
     */
    public function updated(ServiceOrder $serviceOrder): void
    {
        dump('chegou no observer');
        if ($serviceOrder->isDirty('status')) {
            ServiceOrderStatusHistory::create([
                'service_order_id' => $serviceOrder->id,
                'user_id'          => $serviceOrder->user_id,
                'old_status'       => $serviceOrder->getOriginal('status'), 
                'new_status'       => $serviceOrder->status,                
                'changed_at'       => now(),
            ]);
        }
    }

    /**
     * Handle the ServiceOrder "deleted" event.
     */
    public function deleted(ServiceOrder $serviceOrder): void
    {
        //
    }

    /**
     * Handle the ServiceOrder "restored" event.
     */
    public function restored(ServiceOrder $serviceOrder): void
    {
        //
    }

    /**
     * Handle the ServiceOrder "force deleted" event.
     */
    public function forceDeleted(ServiceOrder $serviceOrder): void
    {
        //
    }
}
