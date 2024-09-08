<?php

namespace App\Repositories;

use App\Models\Paiement;
use App\Repositories\PaiementRepository;

class PaiementRepositoryImpl implements PaiementRepository
{
    public function create(array $data)
    {
        return Paiement::create($data);
    }
}
