<?php
namespace App\Repositories;

use App\Services\Dette\ArchiveRepositoryInterface;
use App\Models\ModelFirebase;

class FirebaseArchiveRepository implements ArchiveRepositoryInterface
{
    protected $model;

    public function __construct(ModelFirebase $model)
    {
        $this->model = $model;
    }

    public function getArchivedDebtsByClient($clientId)
    {
        $path = "archive_db2024_09_13";
        return $this->model->find("$path", $clientId);
    }

    public function getAllArchivedDebts()
    {
        $date = date('Y_m_d');
        $path = "archive_db2024_09_13";
        return $this->model->findAll($path);
    }

    public function getArchivedDebtById($debtId){
        $date = date('Y_m_d');
        $path = "archive_db2024_09_13";
        return $this->model->findById($path, $debtId);
    }


    // fonction restaure dette
    public function restoreArchivedDebt($debtId){
        $date = date('Y_m_d');
        $path = "archive_db2024_09_13";
        $debt = $this->getArchivedDebtById($debtId);

        if (empty($debt)) {
            return [
               'status' => 'ERROR',
               'message' => 'Dette archivée non trouvée',
                'code' => 404
            ];
        }

        // Supprimer la dette de Firebase après restauration
        $this->model->deleteById($path, $debtId);

        // Stocker la dette dans la base locale
        $this->model->insert($path, $debt);

        return [
           'status' => 'SUCCESS',
           'message' => 'Dette restaurée avec succès',
            'code' => 200
        ];
    }





}
