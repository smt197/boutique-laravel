<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Demande;
use App\Models\User;
use App\Notifications\DebtReminderNotification;
use App\Services\SmsProviderInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    public function notifySingleClient($clientId)
    {
        $client = Client::findOrFail($clientId);
        $totalDebt = $client->calculateTotalDebt(); // Calculer le total de la dette du client
        
        $smsProvider = app()->make(SmsProviderInterface::class);
        $smsProvider->sendSms($client->telephone, 'Vous avez une dette de ' . $totalDebt . ' non réglée.');

        // Envoi de la notification
        Notification::send($client, new DebtReminderNotification($totalDebt));

        return response()->json(['message' => 'Notification envoyée au client.']);
        
    }

    public function notifyClientByMessage(Request $request)
    {
         // Valider les données envoyées
         $validatedData = $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id',  // S'assurer que chaque ID existe dans la base de données
            'message' => 'required|string',         // Le message personnalisé à envoyer
        ]);

        // Récupérer les clients à notifier
        $clients = Client::whereIn('id', $validatedData['client_ids'])->get();

        // Créer la notification avec le message personnalisé
        $notification = new DebtReminderNotification($validatedData['message']);

        // Envoyer la notification à chaque client dans le groupe
        Notification::send($clients, $notification);

        return response()->json(['message' => 'Notifications envoyées avec succès au groupe.']);
    }

    public function sendNotificationToGroup(Request $request)
    {
        // Valider les IDs de clients envoyés
        $validatedData = $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id',  // S'assurer que chaque ID existe dans la base de données
        ]);

        // Récupérer les clients à notifier
    $clients = Client::whereIn('id', $validatedData['client_ids'])->get();

    // Boucler à travers chaque client pour calculer ses dettes
    foreach ($clients as $client) {
        // Calculer le montant total des dettes pour le client
        $totalDebt = $client->dettes()->sum('montantRestant');

        // Créer une notification pour ce client
        $notification = new DebtReminderNotification($totalDebt);

        // Envoyer la notification à ce client
        Notification::send($client, $notification);
    }

        return response()->json(['message' => 'Notifications envoyées avec succès au groupe.']);
    }


    // Méthode pour récupérer les notifications non lues
    public function getUnreadNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            return [
                'statut' => 'Error',
                'message' => 'Client non authentifié',
                'httpStatus' => 401
            ];
        }

        // Obtenez le modèle Client associé à l'utilisateur
        $client = $user->client;
        
        //dd($client);
        if (!$client) {
            return [
                'statut' => 'Error',
                'data' => null,
                'message' => 'Client non trouvé pour l\'utilisateur.',
                'httpStatus' => 404
            ];
        }

        // Obtenez les notifications non lues du client
        $unreadNotifications = $client->unreadNotifications;

        if($unreadNotifications->isEmpty()) {
            return [
                'statut' => 'Echec',
                'data' => [],
                'message' => 'Aucune notification non lue pour le client.',
                'httpStatus' => 404
            ];
        }

        // Marquez les notifications comme lues
        foreach ($unreadNotifications as $notification) {
            $notification->markAsRead();
        }

        // Retournez les notifications sous format JSON
        return [
            'statut' => 'Success',
            'data' => $unreadNotifications,
            'message' => 'Notifications non lues du client récupérées avec succès.',
            'httpStatus' => 200
        ];
    }

    // Méthode pour récupérer les notifications lues
    public function getReadNotifications(){
        $user = Auth::user();

        if (!$user) {
            return [
                'statut' => 'Error',
                'message' => 'Client non authentifié',
                'httpStatus' => 401
            ];
        }

        // Obtenez le modèle Client associé à l'utilisateur
        $client = $user->client;

        if (!$client) {
            return [
                'statut' => 'Error',
                'data' => null,
                'message' => 'Client non trouvé pour l\'utilisateur.',
                'httpStatus' => 404
            ];
        }

        // Obtenez les notifications lues du client
        $readNotifications = $client->readNotifications;

       
            return [
                'statut' => 'Success',
                'data' => $readNotifications,
                'message' => 'notification lue fetched successfully!',
                'httpStatus' => 404
            ];
    }

    public function getBoutiquierNotifications(){
         // Vérifier que l'utilisateur a le rôle BOUTIQUIER
         $user = Auth::user();

         if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }
         // Vérifier que l'utilisateur a le rôle BOUTIQUIER
         if ($user->role_id !== 2) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }
         // Récupérer les notifications non lues
         $notifications = $user->NotReadBoutiq;
 
         return response()->json([
             'notifications' => $notifications,
         ]);
    }

    public function getDemandes(Request $request)
    {
        // Récupérer le paramètre de statut, s'il existe
        $status = $request->input('status');

        // Construire la requête
        $query = Demande::query();

        // Appliquer le filtre si le statut est fourni
        if ($status) {
            // Validation pour s'assurer que le statut est valide
            if (!in_array($status, ['en attente', 'annulee'])) {
                return response()->json(['message' => 'Statut invalide'], 400);
            }

            // Filtrer par statut
            $query->where('status', $status);
        }

        // Récupérer les demandes
        $demandes = $query->get();

        return response()->json([
            'demandes' => $demandes,
        ]);
    }


    















}   


