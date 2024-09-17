<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DemandeStatusNotification extends Notification
{
    use Queueable;

    protected $demande;
    protected $motif;

    public function __construct($demande, $motif)
    {
        $this->demande = $demande;
        $this->motif = $motif;
    }

    // On utilise uniquement le canal "database"
    public function via($notifiable)
    {
        return ['database'];
    }

    // Les données à enregistrer en base de données
    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demande->id,
            'status' => $this->demande->status,
            'motif' => $this->motif
        ];
    }
}
