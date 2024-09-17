<?php
namespace App\Models;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;

class ModelFirebase
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config_path('projetdette.json'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
        
        $this->database = $factory->createDatabase();
    }

    public function find($path, $clientId)
    {
        $snapshot = $this->database->getReference($path)->getSnapshot();
        $result = [];

        foreach ($snapshot->getValue() as $key => $value) {
            // Assurez-vous que 'client' et 'id' existent dans $value
            if (isset($value['client']['id']) && $value['client']['id'] == $clientId) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function findAll($path)
    {
        $snapshot = $this->database->getReference($path)->getSnapshot();
        $result = [];

        foreach ($snapshot->getValue() as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }

    public function findById($path, $debtId)
    {
        try {
            // Obtenir une référence à la base de données à partir du chemin donné
            $reference = $this->database->getReference($path);
            log::info($reference);
            
            // Rechercher tous les documents dans le chemin donné
            $snapshot = $reference->getSnapshot();

            // Vérifier si le snapshot contient des données
            if (!$snapshot->exists()) {
                Log::info('Aucun document trouvé pour le chemin ' . $path);
                return [
                    'status' => 'ERROR',
                    'message' => 'Aucun document trouvé',
                    'data' => [],
                    'code' => 404
                ];
            }
            $result = [];

            $debtId = (string) $debtId;
            // Parcourir les documents et rechercher celui avec l'ID de dette correspondant
            foreach ($snapshot->getValue() as $key => $document) {
                if (isset($document['dette']['id']) && $document['dette']['id'] == $debtId) {
                    $result[$key] = $document;
                }
            }
            
            // Log des résultats trouvés
            Log::info('Documents trouvés pour ID de dette dans le chemin ' . $path . ' :', $result);

            // Retourner les résultats trouvés
            return [
                'status' => 'SUCCESS',
                'message' => 'Dette récupérée avec succès',
                'data' => $result,
                'code' => 200
            ];

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la dette pour l\'ID ' . $debtId . ' : ' . $e->getMessage());
            return [
                'status' => 'ERROR',
                'message' => 'Erreur lors de la récupération de la dette',
                'data' => [],
                'code' => 500
            ];
        }
    }

    public function insert($path, $data)
{
    try {
        // Obtenir une référence à la base de données à partir du chemin donné
        $reference = $this->database->getReference($path);

        // Ajouter les données dans la base de données
        // Utilisez push() pour ajouter des données sans écraser les existantes
        $reference->push($data);

        // Log de l'insertion réussie
        Log::info('Dette ajoutée avec succès pour le chemin '. $path.':', $data);

        // Retourner un message de succès
        return [
           'status' => 'SUCCESS',
           'message' => 'Dette ajoutée avec succès',
            'code' => 201
        ];

    } catch (\Exception $e) {
        Log::error('Erreur lors de l\'insertion de la dette pour le chemin '. $path.': '. $e->getMessage());
        return [
           'status' => 'ERROR',
           'message' => 'Erreur lors de l\'insertion de la dette',
            'code' => 500
        ];
    }
}


    public function deleteById($path, $debtId){
        try {
            // Obtenir une référence à la base de données à partir du chemin donné
            $reference = $this->database->getReference($path);

            // Supprimer le document avec l'ID de dette correspondant
            // $reference->child($debtId)->remove();

            // Log de la suppression réussie
            Log::info('Dette supprimée avec succès pour le chemin '. $path.'et ID de dette '. $debtId);

            // Retourner un message de succès
            return [
               'status' => 'SUCCESS',
               'message' => 'Dette supprimée avec succès',
                'code' => 200
            ];

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la dette pour le chemin '. $path.'et ID de dette '. $debtId.': '. $e->getMessage(), LOG_DEBUG);
            return [
               'status' => 'ERROR',
               'message' => 'Erreur lors de la suppression de la dette',
                'code' => 500
            ];
        }
    }





}