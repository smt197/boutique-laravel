<?php

namespace App\Services;

use Twilio\Rest\Client;

class SmsService
{
    protected $twilioClient;
    protected $twilioNumber;

    public function __construct()
    {
        $this->twilioClient = new Client(
            env('TWILIO_SID'),
            env('TWILIO_AUTH_TOKEN')
        );

        $this->twilioNumber = env('TWILIO_PHONE_NUMBER');
    }

    public function sendSms($to, $message)
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
