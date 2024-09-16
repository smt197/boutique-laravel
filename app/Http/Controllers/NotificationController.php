<?php
namespace App\Http\Controllers;

use App\Models\Client;
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
    // public function getUnreadNotifications()
    // {
    //     // Récupérer le client connecté
    // $user = Auth::user();

    // // Vérifier l'autorisation d'accès avec la Policy
    // // $this->authorize('viewUnreadNotifications', $user);
    // // Récupérer le client associé à l'utilisateur
    // $client = Client::where('user_id', $user->id)->first();
    
    
    // if (!$client) {
    //     return response()->json(['message' => 'Client non trouvé'], 404);
    // }
    
    // // Récupérer les notifications non lues
    // $unreadNotifications = $user->notifications()->whereNull('read_at')->get();

    //     // Retourner les notifications non lues
    //     return response()->json([
    //         'unread_notifications' => $unreadNotifications,
    //     ], 200);
    // }

    // Méthode pour récupérer les notifications lues
    public function getReadNotifications()
    {
        // Récupérer le client connecté
        $client = Auth::user();

        // Vérifier l'autorisation d'accès avec la Policy
        if (Gate::denies('viewNotifications', $client)) {
            return response()->json(['message' => 'Non autorisé à accéder à ces notifications'], 403);
        }

        // Récupérer les notifications lues
        $readNotifications = $client->readNotifications;

        // Retourner les notifications lues
        return response()->json([
            'read_notifications' => $readNotifications,
        ], 200);
    }
















}   


