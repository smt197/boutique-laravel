<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsChannel;
use App\Services\SmsService;
use Illuminate\Notifications\Notification;

class DebtReminderNotification extends Notification
{
    protected $totalDebt;

    public function __construct($totalDebt)
    {
        $this->totalDebt = $totalDebt;
    }

    public function via($notifiable)
    {
        return ['database', SmsChannel::class]; // Utilise le canal SMS
    }

    public function toSms($notifiable)
    {
        // app(SmsService::class)->sendSms($this->tota, $this->message);
        return "Vous avez une dette totale de " . $this->totalDebt . " non réglée. Merci de régulariser.";
    }

    public function toDatabase($notifiable)
    {
        return [
            'client_id' => $notifiable->id,
            'message' => "Vous avez une dette de " . $this->totalDebt . " non réglée.",
            'montant_total' => $this->totalDebt,
        ];
    }
}
