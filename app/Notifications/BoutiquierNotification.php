<?php
namespace App\Notifications;

use App\Notifications\Channels\SmsChannelPay;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BoutiquierNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $client;

    public function __construct($client)
    {
        $this->client = $client;

    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toDatabase($notifiable)
    {
        return [
            'client_id' => $notifiable->id,
            'message' => "Une nouvelle demande a été soumise par ' . $this->client",
            'montant_rest' => $this->client,
        ];
    }
}
