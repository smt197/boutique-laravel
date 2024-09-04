<?php

namespace App\Services;

use App\Models\Article;

interface ArticleService
{
    public function getAllArticles($disponible = null, array $includes = []);

    public function createArticle(array $data): Article;

    public function findArticleById($id): ?Article;

    public function updateArticle($id, array $data): Article;

    public function deleteArticle($id): bool;

    public function findByLibelle(string $libelle): ?Article;

    public function bulkUpdateStock(array $articles);

    public function updateStockById($id, $quantity): Article;
}
