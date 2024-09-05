<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateQrCodePdf($qrCodeBase64)
    {
        // Générer le PDF à partir de la vue
        $pdf = Pdf::loadView('pdfs.qr_code', compact('qrCodeBase64'));
        
        // Définir le chemin où le PDF sera enregistré
        $pdfFilePath = 'pdfs/client_qrcode_' . time() . '.pdf';
        
        // Enregistrer le fichier PDF dans le stockage public
        \Illuminate\Support\Facades\Storage::put('public/' . $pdfFilePath, $pdf->output());

        // Retourner le chemin d'accès au fichier PDF
        return $pdfFilePath;
    }
}
