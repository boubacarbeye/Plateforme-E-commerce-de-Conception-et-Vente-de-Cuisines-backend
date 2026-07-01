<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ProjetCuisine;
use App\Policies\ProjetPolicy;

class AppServiceProvider extends ServiceProvider
{
    // app/Providers/AppServiceProvider.php
   

    protected $policies = [
        ProjetCuisine::class => ProjetPolicy::class,
    ];
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
        //
    }
}
