<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Models\Article;

class ArticleServiceImpl implements ArticleService
{
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getAllArticles($disponible = null, array $includes = [])
    {
        $filters = [];

        if ($disponible !== null) {
            $filters['disponible'] = $disponible;
        }

        return $this->articleRepository->getAll($filters, $includes);
    }

    public function createArticle(array $data): Article
    {
        return $this->articleRepository->create($data);
    }

    public function findArticleById($id): ?Article
    {
        $article = $this->articleRepository->findById($id);

        if (!$article) {
            throw new \Exception('Article non trouvé');
        }

        return $article;
    }

    public function updateArticle($id, array $data): Article
    {
        $article = $this->articleRepository->update($id, $data);

        if (!$article) {
            throw new \Exception('Échec de la mise à jour de l\'article');
        }

        return $article;
    }

    public function deleteArticle($id): bool
    {
        $success = $this->articleRepository->delete($id);

        if (!$success) {
            throw new \Exception('Échec de la suppression de l\'article');
        }

        return $success;
    }

    public function findByLibelle(string $libelle): ?Article
    {
        return $this->articleRepository->findByLibelle($libelle);
    }

    public function bulkUpdateStock(array $articles)
    {
        return $this->articleRepository->bulkUpdateStock($articles);
    }

    public function updateStockById($id, $quantity): Article
    {
        $article = $this->articleRepository->updateStockById($id, $quantity);

        if (!$article) {
            throw new \Exception('Échec de la mise à jour de la quantité de stock');
        }

        return $article;
    }
    public function findByEtat($etat)
    {
        return $this->articleRepository->findByEtat($etat);
    }
}
