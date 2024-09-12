<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Facades\MongoClientFacade as MongoClient;
use App\Models\Dette;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ArchiveDetteWithMongo implements ShouldQueue
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
        $mongoClient = MongoClient::getClient();
        $db = $mongoClient->selectDatabase('archive_db'); // Sélectionner la base de données
        $collection = $db->selectCollection('archive_db' . Carbon::now()->format('Y_m_d')); // Sélectionner la collection du jour
        
        $dettes = Dette::with(['articles', 'paiements', 'client'])->where('montantRestant', 0)->get();

        foreach ($dettes as $dette) {
            $data = [
                'dette' => $dette->toArray(),
                'articles' => $dette->articles->toArray(),
                'paiements' => $dette->paiements->toArray(),
                'client' => $dette->client->toArray(),
                'archived_at' => now(),
            ];

            $collection->insertOne($data);

            $dette->articles()->delete();
            $dette->paiements()->delete();
            $dette->delete();
        }

        echo "Les dettes soldées ont été archivées avec succès dans MongoDB.\n";
    }
}
