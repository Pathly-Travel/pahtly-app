<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Support\Connectors\OpenAi\OpenAiConnector;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OpenAiConnector::class, function () {
            return new OpenAiConnector();
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
