<?php

namespace App\Observers;

use App\Models\Billing;
use Illuminate\Support\Facades\Cache;

class BillingObserver
{
    /**
     * Handle the Billing "created" event.
     */
    public function created(Billing $billing): void
    {
        //
    }

    /**
     * Handle the Billing "updated" event.
     */
    public function updated(Billing $billing): void
    {
        if ($billing->wasChanged('status')) {
            Cache::forget('dashboard_data');
        }
    }

    /**
     * Handle the Billing "deleted" event.
     */
    public function deleted(Billing $billing): void
    {
        //
    }

    /**
     * Handle the Billing "restored" event.
     */
    public function restored(Billing $billing): void
    {
        //
    }

    /**
     * Handle the Billing "force deleted" event.
     */
    public function forceDeleted(Billing $billing): void
    {
        //
    }
}
