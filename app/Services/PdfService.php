<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;


class PdfService
{
    public function generateQrCodePdf($qrCodeBase64)
    {
        $pdf = Pdf::loadView('pdfs.qr_code', compact('qrCodeBase64'));
        return $pdf->output(); // retourne le contenu binaire du PDF
    }
}
