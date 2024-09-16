<?php

namespace App\Jobs;

use App\Models\Client;
use App\Notifications\PaymentReminderNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendPaymentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         // Récupérer les clients dont la dette a une date d'échéance dépassée
         $clients = Client::whereHas('dettes', function ($query) {
            $query->where('date_echeance', '<', Carbon::now()) // Date dépassée
                  ->where('montantRestant', '>', 0); // Dettes non soldées
        })->get();

        foreach ($clients as $client) {
            // Calcul du total restant
            $totalRemaining = $client->dettes->sum('montantRestant');

            // Créer la notification
            $notification = new PaymentReminderNotification($totalRemaining);

            // Envoyer la notification via plusieurs outils de messagerie
            Notification::send($client, $notification);
        }
    }
}
