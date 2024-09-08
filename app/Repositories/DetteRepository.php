<?php
namespace App\Repositories;

interface DetteRepository
{
    public function create(array $data);
    public function findById($id);
}
