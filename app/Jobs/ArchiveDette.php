<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ArchiveDette implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $archiveChoice = Config::get('services.choice.archive');

        if ($archiveChoice === 'mongodb') {
            dispatch(new ArchiveDetteWithMongo());
        } elseif ($archiveChoice === 'firebase') {
            dispatch(new ArchiveDetteJobWithFireBase());
        } else {
            Log::error('Invalid archive choice configuration');
        }
    }
}