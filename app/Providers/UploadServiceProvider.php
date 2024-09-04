<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UploadService;

class UploadServiceProvider extends ServiceProvider
{
    /**
     * Enregistrer les services dans le conteneur d'application.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UploadService::class, function ($app) {
            return new UploadService();
        });
    }
}
