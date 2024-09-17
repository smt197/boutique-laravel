<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\BoutiquierNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyBoutiquiersOfNewDemandeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        $boutiquiers = User::whereHas('role', function($query) {
            $query->where('nomRole', 'BOUTIQUIER');
        })->get();

        foreach ($boutiquiers as $boutiquier) {
            $boutiquier->notify(new BoutiquierNotification($this->client));
        }
    }
}
