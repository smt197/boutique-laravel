<?php

namespace App\Console;

use App\Jobs\ArchiveDette;
// use App\Jobs\ArchiveDetteWithMongo;
use App\Jobs\SendSmsToClientsWithDebt;
use App\Services\SmsProviderInterface;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Récupérer l'implémentation correcte du service SMS en utilisant l'interface
        //$smsService = app()->make(SmsProviderInterface::class);
        // Planifier le job pour envoyer les SMS
        //$schedule->job(new SendSmsToClientsWithDebt($smsService))->everyMinute();
        //$schedule->command('dettes:archive')->daily();
        $schedule->job(new ArchiveDette())->everyMinute();


    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        \App\Console\Commands\ArchivePaidDebts::class;
        require base_path('routes/console.php');
    }
}
