<?php
namespace App\Repositories;

interface DetteRepository
{
    public function getAll(array $filters = [], array $includes = []);
    public function create(array $data);
    public function findById($id);
    public function findWithArticles($id);
    public function findWithPaiements($id);
    public function addPaiement(int $detteId, float $montant);
}
