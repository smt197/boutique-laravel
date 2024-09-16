<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\SmsProviderInterface;
use App\Notifications\DebtReminderNotification;


class SmsChannel
{
    protected $smsService;

    public function __construct(SmsProviderInterface $smsService)
    {
        $this->smsService = $smsService;
    }
    
    public function send($notifiable, DebtReminderNotification $notification)
    {
        // Vérifier si le modèle peut recevoir des notifications par SMS
        if (!$notifiable->routeNotificationFor('sms')) {
            return;
        }

        // Récupérer le message depuis la notification
        $message = $notification->toSms($notifiable);


        // Envoyer le SMS en utilisant le service choisi (Twilio, InfoBip, etc.)
        $this->smsService->sendSms($notifiable->routeNotificationFor('sms'), $message);
    }
}
