<?php

namespace App\Providers;

use App\Models\ProjetCuisine;
use App\Policies\ProjetPolicy;
use Illuminate\Support\ServiceProvider;

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
