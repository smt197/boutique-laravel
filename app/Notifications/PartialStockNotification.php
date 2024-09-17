<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PartialStockNotification extends Notification
{
    use Queueable;

    protected $disponibilites;

    /**
     * Create a new notification instance.
     *
     * @param  array  $disponibilites
     * @return void
     */
    public function __construct(array $disponibilites)
    {
        $this->disponibilites = $disponibilites;
    }

    /**
     * Determine the channels the notification should be sent on.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'disponibilites' => $this->disponibilites,
            'client_id' => $notifiable->id,
        ];
    }
}
