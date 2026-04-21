<?php

namespace App\Providers;

use App\Models\Billing;
use App\Models\ServiceOrder;
use App\Observers\BillingObserver;
use App\Observers\ServiceOrderObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
        //super admin
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

    }
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('cobrancas_limiter', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });
    }
}
