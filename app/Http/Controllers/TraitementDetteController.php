<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Article;
use App\Models\Dette;
use App\Notifications\DebtCreatedNotification;
use App\Notifications\DemandeStatusNotification;
use Illuminate\Http\Request;
use App\Notifications\PartialStockNotification;
use Illuminate\Support\Facades\DB;

class TraitementDetteController extends Controller
{
    public function checkDisponibilite($id)
    {
        // Récupérer la demande
        $demande = Demande::findOrFail($id);
        
        // Récupérer les articles de la demande
        $articles = json_decode($demande->articles, true); // Convertir JSON en tableau

        $disponibilites = []; // Stocke les informations sur la disponibilité des articles
        $totalDisponible = true;

        // Vérifier la disponibilité de chaque article
        foreach ($articles as $article) {
            $articleData = Article::find($article['article_id']); // Trouver l'article dans la base de données

            // Vérifier si l'article existe et si sa quantité est suffisante
            if ($articleData && $articleData->quantite >= $article['qteVente']) {
                $disponibilites[] = [
                    'article' => $articleData->libelle,
                    'quantite_demandee' => $article['qteVente'],
                    'quantite_disponible' => $articleData->quantite,
                    'disponibilite' => 'Disponible'
                ];
            } else {
                $totalDisponible = false;
                $disponibilites[] = [
                    'article' => $articleData ? $articleData->libelle : 'Inconnu',
                    'quantite_demandee' => $article['quantite'],
                    'quantite_disponible' => $articleData ? $articleData->quantite : 0,
                    'disponibilite' => 'Indisponible'
                ];
            }
        }

        // Si tous les articles sont disponibles
        if ($totalDisponible) {
            return response()->json([
                'data' => [
                    'disponibilites' => $disponibilites,
                    'message' => 'Tous les articles sont disponibles'
                ],
                'status' => 'SUCCESS',
                'message' => 'Opération réussie',
                'code' => 200
            ], 200);
        }

        // Si certains articles ne sont pas disponibles, envoyer une notification
        $client = $demande->client;
        $client->notify(new PartialStockNotification($disponibilites));

        return response()->json([
            'data' => [
                'disponibilites' => $disponibilites,
                'message' => 'Certains articles ne sont pas disponibles en quantité suffisante'
            ],
            'status' => 'PARTIAL_SUCCESS',
            'message' => 'Certains articles manquent',
            'code' => 206
        ], 206);
    }

    // PATCH /api/v1/demandes/{id}
    public function update(Request $request, $id)
    {
        // Récupérer la demande
        $demande = Demande::findOrFail($id);

        // Vérifier que la demande est en attente
        if ($demande->status !== 'en attente') {
            return response()->json(['message' => 'La demande ne peut plus être modifiée'], 400);
        }

        // Récupérer l'action (valider ou annuler)
        $action = $request->input('action');
        $motif = $request->input('motif', null); // Motif d'annulation ou de validation

        DB::beginTransaction();

        try {
            if ($action === 'valider') {
                // Validation de la demande
                $demande->status = 'validée';
                $demande->save();
                
                // Créer une nouvelle dette basée sur la demande
                $dette = Dette::create([
                    'client_id' => $demande->client_id,
                    'montantTotal' => $demande->montant,
                    'articles' => $demande->articles,
                ]);
                dd($dette);

                // Envoyer une notification au client pour l'informer de la validation
                $demande->client->notify(new DebtCreatedNotification($dette));

                DB::commit();

                return response()->json([
                    'message' => 'Demande validée avec succès. Le client a été notifié.',
                    'dette' => $dette
                ], 200);
            } elseif ($action === 'annuler') {
                // Annulation de la demande
                $demande->status = 'annulee';
                $demande->motif_annulation = $motif;
                $demande->save();

                // Envoyer une notification au client pour l'informer de l'annulation
                $demande->client->notify(new DemandeStatusNotification($demande, $motif));

                DB::commit();

                return response()->json([
                    'message' => 'Demande annulée avec succès. Le client a été notifié.',
                ], 200);
            }

            return response()->json(['message' => 'Action non valide'], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Une erreur est survenue'], 500);
        }
    }
}
