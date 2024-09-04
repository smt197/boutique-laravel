<?php

namespace App\Repositories;

use App\Models\Article;

class ArticleRepositoryImpl implements ArticleRepository
{
    public function getAll(array $filters = [], array $includes = [])
    {
        $query = Article::query();

        if (isset($filters['disponible'])) {
            if ($filters['disponible'] === 'oui') {
                $query->where('quantite', '>', 0);
            } elseif ($filters['disponible'] === 'non') {
                $query->where('quantite', '=', 0);
            }
        }

        if (!empty($includes)) {
            $query->with($includes);
        }

        return $query->get();
    }

    public function create(array $data): Article
    {
        return Article::create($data);
    }

    public function findById($id): ?Article
    {
        return Article::find($id);
    }

    public function update($id, array $data): ?Article
    {
        $article = $this->findById($id);

        if ($article) {
            $article->update($data);
        }

        return $article;
    }

    public function delete($id): bool
    {
        $article = $this->findById($id);

        if ($article) {
            return $article->delete();
        }

        return false;
    }

    public function findByLibelle(string $libelle): ?Article
    {
        return Article::where('libelle', $libelle)->first();
    }

    public function bulkUpdateStock(array $articles)
    {
        $updatedArticles = [];
        $notFoundArticles = [];

        foreach ($articles as $articleData) {
            $article = $this->findById($articleData['id']);

            if ($article && $articleData['quantite'] > 0) {
                $article->quantite += $articleData['quantite'];
                $article->save();
                $updatedArticles[] = $article;
            } else {
                $notFoundArticles[] = $articleData;
            }
        }

        return [
            'updatedArticles' => $updatedArticles,
            'notFoundArticles' => $notFoundArticles
        ];
    }

    public function updateStockById($id, $quantity): ?Article
    {
        $article = $this->findById($id);

        if ($article) {
            $article->quantite = $quantity;
            $article->save();
        }

        return $article;
    }

    public function findByEtat($etat)
    {
        return Article::where('etat', $etat)->get();
    }
}
