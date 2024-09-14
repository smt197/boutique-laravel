<?php

namespace App\Repositories;

use App\Models\ModelMongo;
use App\Services\Dette\ArchiveRepositoryInterface;
use App\Models\MongoArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MongoArchiveRepository implements ArchiveRepositoryInterface
{
    protected $model;

    public function __construct(ModelMongo $model)
    {
        $this->model = $model;
    }

    /**
     * Récupère les dettes archivées pour un client spécifique.
     *
     * @param string $clientId
     * @return \MongoDB\Cursor
     */

    public function getArchivedDebtsByClient($clientId)
    {
        return $this->model->find($clientId);
        Log::info($this->model);

    }

     /**
     * Récupère toutes les dettes archivées.
     *
     * @return \MongoDB\Cursor
     */

    public function getAllArchivedDebts()
    {
       
        return $this->model->findAll();
    }
    
    public function getArchivedDebtById($debtId){
        return $this->model->findById($debtId);
    }



    public function restoreArchivedDebt($debtId)
{
    try {
        $archivedDebt = $this->getArchivedDebtById($debtId);
        
        if (empty($archivedDebt)) {
            return [
                'status' => 'ERROR',
                'message' => 'Dette archivée non trouvée',
                'code' => 404
            ];
        }
        
        // Convertir le document BSON en tableau PHP
        $debtArray = $this->convertBSONToArray($archivedDebt);
        
        // Vérifier le format du tableau
        Log::info('Tableau de dette à insérer : ' . print_r($debtArray, true));
        
        // Insérer les données dans PostgreSQL
        DB::table('dettes')->insert($debtArray);
        
        // Supprimer la dette de MongoDB après restauration
        $this->model->deleteArchivedDebt($debtId);

        return [
            'status' => 'SUCCESS',
            'message' => 'Dette restaurée avec succès',
            'code' => 200
        ];
    } catch (\Exception $e) {
        Log::error('Erreur lors de la restauration de la dette : ' . $e->getMessage());
        return [
            'status' => 'ERROR',
            'message' => 'Erreur lors de la restauration de la dette',
            'code' => 500
        ];
    }
}

















    private function convertBSONToArray($bsonDocument)
{
    if ($bsonDocument instanceof \MongoDB\Model\BSONDocument) {
        $bsonDocument = $bsonDocument->getArrayCopy();
    }
    
    if (is_array($bsonDocument)) {
        foreach ($bsonDocument as &$value) {
            $value = $this->convertBSONToArray($value);
        }
    }
    
    return $bsonDocument;
}




}