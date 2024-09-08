<?php
namespace App\Repositories;

use App\Models\Dette;
use App\Repositories\DetteRepository;

class DetteRepositoryImpl implements DetteRepository
{
    public function create(array $data)
    {
        return Dette::create($data);
    }

    public function findById($id)
    {
        return Dette::find($id);
    }
}

