<?php

namespace App\Services;

use Twilio\Rest\Client;

class SmsService implements SmsProviderInterface
{
    protected $twilioClient;
    protected $twilioNumber;

    public function __construct()
    {
        $this->twilioClient = new Client(
            'ACa9c3186f84f5089b3b471ecc2bbf4e5f',
            '6ba0dfc598bdad3556c0ad7e6c821146'
        );

        $this->twilioNumber = '+12138934162';
    }

    public function sendSms($to, $message): void
    {
        try {
            // Formater le numéro avant d'envoyer le SMS
            $formattedNumber = $this->formatPhoneNumber($to);

            $this->twilioClient->messages->create($formattedNumber, [
                'from' => $this->twilioNumber,
                'body' => $message,
            ]);
        } catch (\Exception $e) {
            // Gestion des erreurs
            throw new \Exception("Erreur lors de l'envoi du SMS : " . $e->getMessage());
        }
    }

    // Fonction pour formater le numéro de téléphone
    private function formatPhoneNumber($phone)
    {
        // Ajouter l'indicatif si absent (exemple: Sénégal = +221)
        if (!preg_match("/^\+221/", $phone)) {
            return '+221' . $phone;
        }
        return $phone;
    }
}
