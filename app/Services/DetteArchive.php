<?php
namespace App\Services;

use App\Models\Dette;
use App\Services\IMongoDB;
use PHPUnit\Event\TestSuite\Loaded;

class DetteArchive
{
    /*protected $mongoClient;

    public function __construct(IMongoDB $mongoConnection)
    {
        // Obtenir le client MongoDB
        $this->mongoClient = $mongoConnection->getClient();
    }

    public function archivePaidDebt(Dette $dette)
    {
        // Si la dette est totalement payée
        if ($dette->montantRestant == 0) {
            
            // Sélectionner la base de données et la collection
            $database = $this->mongoClient->selectDatabase(env('MONGO_DB_DATABASE'));
            $collection = $database->selectCollection('archive_db');


            // Archiver la dette dans MongoDB
            $collection->insertOne([
                'dette_id' => $dette->id,
                'client_id' => $dette->client_id,
                'montantTotal' => $dette->montantTotal,
                'montantVerse' => $dette->montantVerse,
                'montantRestant' => $dette->montantRestant,
                'dateArchive' => now(),
            ]);

            // Supprimer la dette de PostgreSQL si nécessaire
            $dette->delete();
        }
    }*/
}
