<?php

namespace App\Observers;

use App\Models\Dette;
use App\Models\Paiement;
use App\Models\Article;
use Illuminate\Support\Facades\DB;

class DetteObserver
{
    /**
     * Handle the Dette "created" event.
     */
    public function created(Dette $dette): void
    {
        DB::beginTransaction();

        try {
            // Ajouter les articles et mettre à jour le stock
            foreach (request()->articles as $articleData) {
                $article = Article::findOrFail($articleData['articleId']);

                if (!$article) {
                    throw new \Exception("L'article avec l'ID '{$article}' n'existe pas.");
                }
                 // Vérifier si le stock est suffisant
                 if ($article->quantite < $articleData['qteVente']) {
                    throw new \Exception("Le stock de l'article '{$article->reference}' est insuffisant.");
                }
                $article->quantite -= $articleData['qteVente'];
                $article->save();

                // Attacher l'article à la dette avec l'ID maintenant disponible
                $dette->articles()->attach($articleData['articleId'], [
                    'qteVente' => $articleData['qteVente'],
                    'prixVente' => $articleData['prixVente']
                ]);
            }

            // Ajouter un paiement si présent
            if (isset(request()->paiement['montant'])) {
                $paiement = new Paiement();
                $paiement->montant = request()->paiement['montant'];
                $paiement->dette_id = $dette->id;

                //En cas d'ajout d'un paiement  , le montant devra etre inferieur ou egal au montant de la dette
                if ($dette->montantRestant < request()->paiement['montant']) {
                    throw new \Exception("Le montant du paiement est supérieur au montant de la dette.");
                }
                $paiement->save();

                // Recalculer le montant restant après le paiement
                $dette->montantRestant = $dette->montantTotal - $dette->montantVerse;
                $dette->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle the Dette "updated" event.
     */
    public function updated(Dette $dette): void
    {
        //
    }

    // Other methods...
}
