<?php

namespace App\Jobs;

use App\Facades\FirebaseClientFacade;
use App\Models\Dette;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ArchiveDetteJobWithFireBase implements ShouldQueue
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
    // Récupérer les dettes soldées de la base de données locale
    DB::beginTransaction();
    $dettes = Dette::with(['articles', 'paiements', 'client'])->where('montantRestant', 0)->get();

    // Définir une référence de collection pour archiver par jour
    $firebaseClient = FirebaseClientFacade::getCollection('archive_db' . Carbon::now()->format('Y_m_d'));

    foreach ($dettes as $dette) {
        // Préparer les données pour l'archivage
        $data = [
            'dette' => $dette->toArray(),
            'articles' => $dette->articles->toArray(),
            'paiements' => $dette->paiements->toArray(),
            'client' => $dette->client->toArray(),
            'archived_at' => now(),
        ];

        // Insérer les données dans Firebase
        $firebaseClient->push($data); // Pas besoin d'appeler getReference()

        // Supprimer les données locales une fois archivées
        $dette->articles()->delete();
        $dette->paiements()->delete();
        $dette->delete();
    }

    DB::commit(); 

    echo "Les dettes soldées ont été archivées avec succès dans Firebase.\n";
}

}