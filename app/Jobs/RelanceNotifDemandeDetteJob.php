<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Notification;
use App\Models\Demande;
use App\Notifications\RelanceNotificationDemande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RelanceNotifDemandeDetteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $demande;

    public function __construct(Demande $demande)
    {
        $this->demande = $demande;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Notification::send($this->demande->client, new RelanceNotificationDemande($this->demande));

    }
}
