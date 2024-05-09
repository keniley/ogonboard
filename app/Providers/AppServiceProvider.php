<?php

namespace App\Providers;

use App\Interfaces\TicketServiceInterface;
use App\Interfaces\WorkDayServiceInterface;
use App\Services\TicketService;
use App\Services\WorkDayService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WorkDayServiceInterface::class, function () {
            return new WorkDayService();
        });

        $this->app->singleton(TicketServiceInterface::class, function ($app) {
            return new TicketService($app->make(WorkDayServiceInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
