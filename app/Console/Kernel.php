<?php

namespace App\Console;

use App\Jobs\ArchiveDetteWithMongo;
use App\Jobs\SendSmsToClientsWithDebt;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->job(new SendSmsToClientsWithDebt(app()->make('App\Services\SmsService')))->veryMinute();;
        $schedule->job(new SendSmsToClientsWithDebt(app()->make('App\Services\SmsService')))->everyMinute();
        //$schedule->command('dettes:archive')->daily();
        $schedule->job(new ArchiveDetteWithMongo)->everyFiveSeconds();


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
