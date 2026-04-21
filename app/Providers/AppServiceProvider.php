<?php

namespace App\Providers;

use App\Models\Billing;
use App\Models\ServiceOrder;
use App\Observers\BillingObserver;
use App\Observers\ServiceOrderObserver;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter as FacadesRateLimiter;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ServiceOrder::observe(ServiceOrderObserver::class);
        Billing::observe(BillingObserver::class);
        $this->configureRateLimiting();
    }
    protected function configureRateLimiting(): void
    {
        FacadesRateLimiter::for('cobrancas_limiter', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });
    }
}
