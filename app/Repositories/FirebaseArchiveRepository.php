<?php
namespace App\Repositories;

use App\Services\Dette\ArchiveRepositoryInterface;
use App\Models\ModelFirebase;
use Illuminate\Support\Facades\Log;

class FirebaseArchiveRepository implements ArchiveRepositoryInterface
{
    protected $model;

    public function __construct(ModelFirebase $model)
    {
        $this->model = $model;
    }

    public function getArchivedDebtsByClient($clientId)
    {
        $path = "archive_db2024_09_17";
        return $this->model->find("$path", $clientId);
    }

    public function getAllArchivedDebts()
    {
        $date = date('Y_m_d');
        $path = "archive_db2024_09_17";
        return $this->model->findAll($path);
    }

    public function getArchivedDebtById($debtId){
        $date = date('Y_m_d');
        $path = "archive_db2024_09_17";
        return $this->model->findById($path, $debtId);
    }


    // fonction restaure dette
    public function restoreArchivedDebt($debtId)
    {
        $path = "archive_db2024_09_17";
        $debt = $this->getArchivedDebtById($debtId);
    
        if (empty($debt) || $debt['status'] == 'ERROR') {
            return [
               'status' => 'ERROR',
               'message' => 'Dette archivée non trouvée',
                'code' => 404
            ];
        }
    
        // Supprimer la dette de Firebase après récupération
        $this->model->deleteById($path, $debtId);
    
        // Stocker la dette dans la base locale
        // Assurez-vous que $debt['data'] contient les données au format attendu pour PostgreSQL
        $debtData = $debt['data'];
        $this->storeInLocalDatabase($debtData);
    
        return [
           'status' => 'SUCCESS',
           'message' => 'Dette restaurée avec succès',
            'code' => 200
        ];
    }
    


    protected function storeInLocalDatabase($data)
{
    try {
        // Assurez-vous que les données nécessaires sont présentes
        $formattedData = [
            'montantRestant' => isset($data['montantRestant']) ? (float) $data['montantRestant'] : 0.00,
            'montantTotal' => isset($data['montantTotal']) ? (float) $data['montantTotal'] : 0.00,
            'montantVerse' => isset($data['montantVerse']) ? (float) $data['montantVerse'] : 0.00,
            'archived_at' => isset($data['archived_at']) ? \Carbon\Carbon::parse($data['archived_at']) : null,
            'client_id' => isset($data['client']['id']) ? (int) $data['client']['id'] : null,
            // Ajoutez d'autres champs nécessaires ici
        ];

        // Créez un modèle et remplissez-le avec les données formatées
        $debtModel = new \App\Models\Dette();
        $debtModel->fill($formattedData);
        $debtModel->save();
        
        // Log de la sauvegarde réussie
        Log::info('Dette stockée avec succès dans la base de données locale:', $formattedData);
        
        return [
            'status' => 'SUCCESS',
            'message' => 'Dette stockée dans la base de données locale',
            'code' => 201
        ];
    } catch (\Exception $e) {
        Log::error('Erreur lors du stockage de la dette dans la base de données locale: ' . $e->getMessage());
        return [
            'status' => 'ERROR',
            'message' => 'Erreur lors du stockage de la dette dans la base de données locale',
            'code' => 500
        ];
    }
}

    



}
