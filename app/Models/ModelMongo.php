<?php
namespace App\Models;

use Illuminate\Support\Facades\Log;
use MongoDB\Client as MongoClient;
use MongoDB\Collection;

class ModelMongo
{
    protected $collection;
    protected $database;


    public function __construct()
    {
        // Configuration de la connexion MongoDB
        $client = new MongoClient(env('MONGO_DB_CONNECTION_STRING')); // URL de connexion MongoDB
        $this->database = $client->selectDatabase('archive_db'); // Nom de votre base de données
        // $this->collection = $this->database->selectCollection('archive_db'); // Nom de votre collection
    }
    
   
    public function find($clientId)
    {
        try {
            // Obtenir la liste des collections dans la base de données
            $collections = $this->database->listCollections();
            $result = [];
    
            // Parcourir chaque collection
            foreach ($collections as $collection) {
                $collectionName = $collection->getName();
                
                // Rechercher des documents avec l'ID du client dans la collection actuelle
                $clientId = (int)$clientId;
                $criteria = ['client.id' => $clientId];
                $documents = $this->database->selectCollection($collectionName)->find($criteria)->toArray();
                log::info($result);
                
                // Stocker les résultats s'il y en a
                if (!empty($documents)) {
                    $result[$collectionName] = $documents;
                }
            }
            
            // Retourner les résultats si des documents sont trouvés
            return $result;
    
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des dettes pour le client : ' . $e->getMessage());
            return [];
        }
    }

    public function findById($debtId)
    {
        try {
            // Assurez-vous que l'ID de la dette est une chaîne de caractères
            $debtId = (int) $debtId;
            
            // Obtenir la liste des collections dans la base de données
            $collections = $this->database->listCollections();
            $result = [];
    
            // Parcourir chaque collection
            foreach ($collections as $collection) {
                $collectionName = $collection->getName();
                
                // Rechercher des documents avec l'ID de la dette dans la collection actuelle
                $criteria = ['dette.id' => $debtId]; // Assurez-vous que le champ correspond à votre modèle de données
                $documents = $this->database->selectCollection($collectionName)->find($criteria)->toArray();
                
                // Log des documents trouvés
                Log::info('Documents trouvés pour ID de dette dans collection ' . $collectionName . ' :', $documents);
                
                // Stocker les résultats s'il y en a
                if (!empty($documents)) {
                    $result[$collectionName] = $documents;
                }
            }
            
            // Retourner les résultats si des documents sont trouvés
            return $result;
    
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la dette pour l\'ID : ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Trouve toutes les dettes archivées.
     *
     * @return \MongoDB\Cursor
     */
    public function findAll()
    {
       // Obtenir la liste des collections
       $collections = $this->database->listCollections();
       $result = [];

       foreach ($collections as $collection) {
           $collectionName = $collection->getName();
           // Obtenir tous les documents dans la collection
           $documents = $this->database->selectCollection($collectionName)->find()->toArray();
           $result[$collectionName] = $documents;
       }

       return $result;
    }


    public function deleteArchivedDebt($debtId)
    {
        try {
            $collections = $this->database->listCollections();

            foreach ($collections as $collection) {
                $collectionName = $collection->getName();
                $result = $this->database->selectCollection($collectionName)->deleteOne(['dette.id' => (int)$debtId]);
                
                if ($result->getDeletedCount() > 0) {
                    Log::info("Dette supprimée de la collection $collectionName");
                    return true;
                }
            }

            Log::warning("Aucune dette trouvée avec l'ID $debtId pour suppression");
            return false;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la dette archivée : ' . $e->getMessage());
            return false;
        }
    }
}
