<?php

namespace App\Services;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class EmailService
{
    public function sendQrCodeEmail($email, $pdfContent)
    {
        // Envoi de l'email avec le PDF en pièce jointe
        Mail::send([], [], function ($message) use ($email, $pdfContent) {
            $message->to($email)
                    ->subject('Votre QR Code')
                    // ->setBody('Veuillez trouver votre QR Code en pièce jointe.', 'text/html')
                    ->attachData($pdfContent, 'qrcode.pdf', [
                        'mime' => 'application/pdf',
                    ]);
        });
    }
}
