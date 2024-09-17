<?php

namespace App\Notifications;

use App\Models\Dette;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DebtCreatedNotification extends Notification
{
    use Queueable;

    protected $dette;

    public function __construct(Dette $dette)
    {
        $this->dette = $dette;
    }

    // Utilisation uniquement du canal "database"
    public function via($notifiable)
    {
        return ['database'];
    }

    // Les données à enregistrer en base de données
    public function toArray($notifiable)
    {
        return [
            'dette_id' => $this->dette->id,
            'montant' => $this->dette->montantTotal,
            'message' => 'Votre dette a été créée avec succès. Veuillez récupérer vos produits.'
        ];
    }
}
