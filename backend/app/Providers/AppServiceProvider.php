<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Providers\AIProviderInterface;
use App\Services\Providers\MockAIProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the interface to the Mock Provider
        $this->app->bind(AIProviderInterface::class, MockAIProvider::class);
    }

    public function boot(): void
    {
        //
    }
}