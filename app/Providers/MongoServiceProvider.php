<?php

namespace App\Providers;

use App\Services\IMongoDB;
use App\Services\IMongoImpl;
use Illuminate\Support\ServiceProvider;
use MongoDB\Client as MongoClient;

class MongoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(IMongoDB::class, IMongoImpl::class);

        // $this->app->singleton(DetteArchive::class, function ($app) {
        //     return new DetteArchive($app->make(IMongoDB::class));
        // });
    }
}