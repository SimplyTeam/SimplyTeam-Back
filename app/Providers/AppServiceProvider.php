<?php

namespace App\Providers;

use App\Http\Controllers\ProjectController;
use App\Repositories\ProjectRepository;
use App\Services\ProjectService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositories

        // Services
        $this->app->bind(
            ProjectService::class,
            fn($container) => new ProjectService($container->get(ProjectRepository::class))
        );

        // Controllers

        $this->app->bind(
            ProjectController::class,
            fn($container) => new ProjectController($container->get(ProjectService::class))
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
