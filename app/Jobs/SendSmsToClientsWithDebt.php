<?php

namespace App\Jobs;

use App\Models\Client;
use App\Notifications\DebtReminderNotification;
use App\Services\SmsProviderInterface;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsToClientsWithDebt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $smsService;

    public function __construct(SmsProviderInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $clients = Client::whereHas('dettes', function ($query) {
            $query->where('montantRestant', '>', 0);
        })->with('dettes')->get();

        foreach ($clients as $client) {
            $totalDebt = $client->dettes->sum('montantRestant');
            $message = "Vous avez une dette de $totalDebt non réglée. Veuillez régulariser votre situation.";

            $this->smsService->sendSms($client->telephone, $message);
            $notification = new DebtReminderNotification($totalDebt);
            $client->notify($notification);

        }
    }
}
