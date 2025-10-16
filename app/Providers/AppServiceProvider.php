<?php

namespace App\Providers;

use App\Models\Variable;
use App\Observers\VariableObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        URL::forceHttps();

        Variable::observe(VariableObserver::class);
    }
}
