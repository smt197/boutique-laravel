<?php

namespace App\Services;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

class InfoBipSmsService implements SmsProviderInterface
{
    public function sendSms( string $to, string $message ): void{
        // Créer une instance de client HTTP
        $client = new Client();
    
        // Définir l'URL de l'API
        $url = 'https://vvpk9v.api.infobip.com/sms/2/text/advanced';
    
        // Définir les données du corps de la requête
        $body = [
            'messages' => [
                [
                    'destinations' => [
                        ['to' => '221' . $to]
    
                    ],
                    'from' => '447491163443',
                    'text' => 'Hello World!',
    
                    'text' => $message
                ]
            ]
        ];
    
        // Envoyer la requête POST
        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'App c49b48b0eb41b4fe4b12195ed919e7d9-db6c5adb-d4f2-4b6f-8669-46f557258b81',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $body
            ]);
    
            // Vérifier si la requête a réussi
            if ($response->getStatusCode() == 200) {
                echo $response->getBody();
            } else {
                echo 'Unexpected HTTP status: ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase();
            }
        } catch (RequestException $e) {
            // Gérer les erreurs
            echo 'Error: ' . $e->getMessage();
        }
    }
}
