<?php

namespace App\Services\Dette;

interface ArchiveRepositoryInterface
{
    public function getArchivedDebtsByClient($clientId);
    public function getAllArchivedDebts();
    public function getArchivedDebtById($debtId);
    public function restoreArchivedDebt($debtId); 

}