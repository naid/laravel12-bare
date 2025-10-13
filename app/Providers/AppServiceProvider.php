<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        // Share selected client with all views
        View::composer('*', function ($view) {
            $selectedClient = session('selected_client');
            $selectedClientId = session('selected_client_id');
            
            $view->with('selectedClient', $selectedClient);
            $view->with('selectedClientId', $selectedClientId);
        });
    }
}
