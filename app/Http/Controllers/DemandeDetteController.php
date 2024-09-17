<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use Illuminate\Http\Request;
use App\Jobs\NotifyBoutiquiersOfNewDemandeJob;
use App\Http\Requests\StoreDemandeRequest;
use App\Jobs\RelanceNotifDemandeDetteJob;
use Carbon\Carbon;

class DemandeDetteController extends Controller
{

    public function index(Request $request)
    {
        // Récupérer l'utilisateur authentifié
        $user = auth()->user();
        $client = $user->client; // Récupérer le client à partir du token

        if (!$client) {
            return response()->json(['message' => 'Vous devez vous connecter pour effectuer cette action'], 401);
        }

        // Initialiser la requête de demande pour le client
        $query = Demande::where('client_id', $client->id);

        if ($request->has('etat')) {
            $etat = $request->query('etat');
            $etatValide = ['en attente', 'annulee']; 

            if (in_array($etat, $etatValide)) {
                $query->where('status', $etat);
            } else {
                return response()->json(['error' => 'État invalide'], 400);
            }
        }

        // Récupérer les demandes
        $demandes = $query->get();

        return response()->json(['demandes' => $demandes]);
    }


        public function store(StoreDemandeRequest $request)
        {
            // Récupérer l'utilisateur authentifié
            $user = auth()->user();
            $client = $user->client; // Récupérer le client à partir du token

            if (!$client) {
                return response()->json(['message' => 'Vous devez vous connecter pour effectuer cette action'], 401);
            }

            // Validation des données
            $validatedData = $request->validated();


            // Vérification des conditions pour soumettre une demande
            if ($this->canMakeDebtRequest($client, $validatedData['montant'])) {
                // Création de la demande
                $demande = Demande::create([
                    'client_id' => $client->id,
                    'montant' => $validatedData['montant'],
                    'status' => 'en attente', // Statut de défaut
                    'articles' => json_encode($validatedData['articles']),

                ]);

                        // Appliquer les règles par catégorie
                if ($client->categorie_id == 3) { // Bronze
                    if ($client->dettes()->count() > 0) {
                        return response()->json(['error' => 'Les clients Bronze ne peuvent pas avoir de dettes.'], 400);
                    }
                } elseif ($client->categorie_id == 2) { // Silver
                    $totalDette = $client->dettes()->sum('montantTotal');
                    if ($totalDette >= $client->max_montant) {
                        return response()->json(['error' => 'Le montant maximum pour les clients Silver est atteint.'], 400);
                    }
                }

                // Charger la relation avec le client
                $demande->load('client');

                // Enregistrer la demande
                $demande->save();

                // Notification ou tout autre traitement post-création
                // Exécution d'un Job pour notifier les utilisateurs de rôle 'BOUTIQUIER'
                dispatch(new NotifyBoutiquiersOfNewDemandeJob($demande));

                return response()->json(['message' => 'Demande de dette enregistrée avec succès', 'demande' => $demande], 201);
            }

            return response()->json(['message' => 'Vous ne pouvez pas soumettre cette demande'], 403);
        }

    // Relance notification

    public function relance($id)
    {
        $demande = Demande::find($id);

        if (!$demande) {
            return response()->json(['message' => 'Demande non trouvée'], 404);
        }

        if ($demande->status !== 'annulee') {
            return response()->json(['message' => 'La demande doit être annulée pour envoyer une relance'], 400);
        }

        // Planifier le job de relance dans 2 jours
        $delai = Carbon::now()->addDays(2);
        RelanceNotifDemandeDetteJob::dispatch($demande)->delay($delai);

        return response()->json(['message' => 'La relance sera envoyée dans 2 jours'], 200);
    }

    // Méthode pour vérifier si le client peut soumettre une demande de dette
    public function canMakeDebtRequest($client, $montant)
    {
        if ($client->categorie->libelle === 'Gold') {
            return true;
        }

        if ($client->categorie->libelle === 'Silver' && $client->max_montant >= $montant) {
            return true;
        }

        if ($client->categorie->libelle === 'Bronze' && $client->dettes->isEmpty()) {
            return true;
        }

        return false;
    }
}
