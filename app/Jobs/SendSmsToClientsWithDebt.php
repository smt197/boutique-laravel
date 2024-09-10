<?php

namespace App\Jobs;

use App\Models\Client;
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

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $clients = Client::whereHas('dettes', function ($query) {
            $query->where('montantRestant', '>', 0);
        })->get();

        foreach ($clients as $client) {
            $this->smsService->sendSms($client->telephone, 'Vous avez une dette non réglée. Veuillez régulariser votre situation.');
        }
    }
}
