<?php

namespace App\Observers;

use App\Models\ServiceOrder;

class ServiceOrderObserver
{
    /**
     * Handle the ServiceOrder "created" event.
     */
    public function created(ServiceOrder $serviceOrder): void
    {
        //
    }

    /**
     * Handle the ServiceOrder "updated" event.
     */
    public function updated(ServiceOrder $serviceOrder): void
    {
        //
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
