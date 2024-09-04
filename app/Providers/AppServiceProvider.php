<?php

namespace App\Providers;

use App\Repositories\ArticleRepository;
use App\Repositories\ArticleRepositoryImpl;
use App\Repositories\ClientRepository;
use App\Repositories\ClientRepositoryImpl;
use App\Services\ArticleService;
use App\Services\ArticleServiceImpl;
use App\Services\ClientService;
use App\Services\ClientServiceImpl;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ClientRepository::class, ClientRepositoryImpl::class);
        $this->app->singleton(ClientService::class, ClientServiceImpl::class);

        $this->app->singleton(ArticleRepository::class, ArticleRepositoryImpl::class);
        $this->app->singleton(ArticleService::class, ArticleServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
