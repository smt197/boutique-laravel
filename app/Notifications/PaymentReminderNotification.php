<?php
namespace App\Notifications;

use App\Notifications\Channels\SmsChannelPay;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $totalRemaining;

    public function __construct($totalRemaining)
    {
        $this->totalRemaining = $totalRemaining;
    }

    public function via($notifiable)
    {
        return ['database', SmsChannelPay::class]; // Envoi via SMS et stocké dans la DB
    }

    public function toSms($notifiable)
    {
        return "Votre dette est en echeance. Il vous reste " . $this->totalRemaining . " à payer.";
    }

    public function toDatabase($notifiable)
    {
        return [
            'client_id' => $notifiable->id,
            'message' => "Votre dette est echeance. Il vous reste " . $this->totalRemaining . " à payer.",
            'montant_rest' => $this->totalRemaining,
        ];
    }
}
