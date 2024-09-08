<?php
namespace App\Services;

use App\Models\Dette;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

class DetteServiceImpl implements DetteService
{
    public function store(array $data)
    {
        DB::beginTransaction();

        try {
            // Créer une nouvelle dette
            $dette = new Dette();
            $dette->montantTotal = $data['montant'];
            $dette->client_id = $data['clientId'];
            $dette->montantRestant = $data['montant']; // Initialement, montantRestant = montantTotal
            $dette->save();

            // Ajouter les articles
            foreach ($data['articles'] as $article) {
                // On suppose que vous avez un pivot 'article_dette' pour gérer cette relation
                $dette->articles()->attach($article['articleId'], [
                    'qteVente' => $article['qteVente'],
                    'prixVente' => $article['prixVente']
                ]);
            }

            // Ajouter le paiement si présent
            if (isset($data['paiement']['montant'])) {
                $paiement = new Paiement();
                $paiement->montant = $data['paiement']['montant'];
                $paiement->dette_id = $dette->id;
                $paiement->save();

                // Recalculer le montantRestant après le paiement
                $dette->montantRestant = $dette->montantTotal - $dette->montantVerse;
                $dette->save(); // Sauvegarder la mise à jour de montantRestant

            }

            DB::commit();

            return $dette;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
