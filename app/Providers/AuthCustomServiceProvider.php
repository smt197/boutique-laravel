<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Authentication\AuthenticationServiceInterface;
use App\Services\Authentication\AuthenticationPassport;
use App\Services\Authentication\AuthenticationSanctum;

class AuthCustomServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuthenticationServiceInterface::class, function ($app) {
            // Choisir l'implémentation en fonction de la configuration
            $driver = config('auth.default_driver');
            
            return $driver === 'passport' 
                ? new AuthenticationPassport()
                : new AuthenticationSanctum();
        });
    }

    public function boot()
    {
        //
    }
}