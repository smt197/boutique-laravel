<?php

namespace App\Repositories;

use App\Models\Article;

interface ArticleRepository
{
    public function getAll(array $filters = [], array $includes = []);

    public function create(array $data): Article;

    public function findById($id): ?Article;

    public function update($id, array $data): ?Article;

    public function delete($id): bool;

    public function findByLibelle(string $libelle): ?Article;
    
    public function findByEtat($etat);

    public function bulkUpdateStock(array $articles);

    public function updateStockById($id, $quantity): ?Article;
}
