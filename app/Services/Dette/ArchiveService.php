<?php

namespace App\Services\Dette;

use App\Services\Dette\ArchiveRepositoryInterface;

class ArchiveService
{
    protected $repository;

    public function __construct(ArchiveRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getArchivedDebtsByClient($clientId)
    {
        return $this->repository->getArchivedDebtsByClient($clientId);
    }
    public function getAllArchivedDebts()
    {
        return $this->repository->getAllArchivedDebts();
    }
    public function getArchivedDebtById($debtId){
        return $this->repository->getArchivedDebtById($debtId);
    }

    public function restoreArchivedDebt($debtId)
    {
        return $this->repository->restoreArchivedDebt($debtId);
    }
}