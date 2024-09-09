<?php
namespace App\Repositories;

use App\Models\Dette;
use App\Repositories\DetteRepository;

class DetteRepositoryImpl implements DetteRepository
{
    public function getAll(array $filters = [], array $includes = [])
    {

        $query = Dette::query();

        if (isset($filters['statut'])) {
            if ($filters['statut'] === 'Solde') {
                $query->where('montantRestant', '=', 0);
            } elseif ($filters['statut'] === 'nonSolde') {
                $query->where('montantRestant', '!=', 0);
            }
        }

        if (!empty($includes)) {
            $query->with($includes);
        }

        return $query->get();
    }
    
    public function create(array $data)
    {
        return Dette::create($data);
    }

    public function findById($id)
    {
        return Dette::find($id);
    }


    public function findWithArticles($id)
    {
        return Dette::with('article:id,reference,libelle,prix,quantite')->find($id);
    }

    public function findWithPaiements($id)
    {
        return Dette::with('paiements:id,montant')->find($id);
    }
    
    public function addPaiement(int $detteId, float $montant)
    {
        $dette = Dette::find($detteId);

        // Vérifie que le montant est positif et inférieur ou égal au montant restant
        if ($montant <= 0 || $montant > $dette->montantRestant) {
            throw new \Exception('Le montant doit être positif et inférieur ou égal au montant restant');
        }

        // Mettre à jour les montants dans la dette
        $dette->update([
            'montantRestant' => $dette->montantRestant - $montant,
            // 'montant' => $dette->montant + $montant
        ]);

        // Ajouter le paiement
        $dette->paiements()->create([
            'montant' => $montant,
            // Ajoute d'autres champs si nécessaire
        ]);
        return $dette;
    }
}

