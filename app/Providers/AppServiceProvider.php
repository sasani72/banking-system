<?php

namespace App\Providers;

use App\Domain\Services\CustomStoredEventsService;
use App\Domain\Repositories\AccountRepository;
use App\Domain\Services\AccountService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AccountRepository::class, function ($app) {
            return new AccountRepository($app->make(CustomStoredEventsService::class));
        });

        $this->app->bind(AccountService::class, AccountService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(125);
    }
}
