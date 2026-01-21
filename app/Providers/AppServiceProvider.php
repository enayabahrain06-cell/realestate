<?php

namespace App\Providers;

use App\Models\Unit;
use App\Models\AuditLog;
use App\Models\Report;
use App\Observers\UnitObserver;
use Illuminate\Support\ServiceProvider;
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
        // Register Unit observer for automatic history tracking
        Unit::observe(UnitObserver::class);

        // Define policies
        Gate::policy(AuditLog::class, \App\Policies\AuditLogPolicy::class);
        Gate::policy(Report::class, \App\Policies\ReportPolicy::class);
    }
}
