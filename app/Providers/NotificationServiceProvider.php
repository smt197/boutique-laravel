<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SmsProviderInterface;
use App\Services\InfoBipSmsService; // Ou un autre service
use App\Services\SmsService;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(SmsProviderInterface::class, function ($app) {
            $service = env('SMS_SERVICE','twilio');
            if ($service === 'infobip') {
                return new InfoBipSmsService();
            }
            return new SmsService();
        });
    }
}
