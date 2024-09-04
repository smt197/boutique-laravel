<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Models\LoyaltyCard;

class QrCodeService
{
    /**
     * Génère un QR code en base64 pour un numéro de téléphone donné.
     *
     * @param string $telephone
     * @return string
     */
    public function generateBase64QrCode($telephone)
    {
        $render = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );

        $qrCode = new Writer($render);
        $qrCodeImage = $qrCode->writeString($telephone);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCodeImage);
    }

    /**
     * Crée une carte de fidélité pour un client.
     *
     * @param string $surname
     * @param string $telephone
     * @param string|null $photoBase64
     * @param string $qrCodeBase64
     * @return void
     */
    public function createLoyaltyCard(string $surname, string $telephone, ?string $photoBase64, string $qrCodeBase64): void
    {
        try {
            // Création de la carte de fidélité
            LoyaltyCard::create([
                'surname' => $surname,
                'telephone' => $telephone,
                'photo' => $photoBase64,
                'qr_code' => $qrCodeBase64        
            ]);
        } catch (\Exception $e) {
            // Gérer l'exception selon vos besoins
            $e->getMessage();
        }
    }
}
