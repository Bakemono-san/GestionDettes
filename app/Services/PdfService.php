<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Illuminate\Support\Facades\Log;

class PdfService
{
    public function generatePdf2($qrcode, $photoBase64, $userData)
    {
        
        // Define the path where the PDF will be saved
        $filePath = storage_path('app/public/cartes/');

        // Generate the PDF
        $pdf = DomPdf::loadView('pdf.pdf', [
                'qrcode' => $qrcode,
                'profile' => $photoBase64,
                'user' => $userData
            ]);

        // Save the PDF to the specified path
        $pdf->save($filePath.$userData['login'].'.pdf');

        return $filePath.$userData['login'].'.pdf'; // Optionally return the path of the saved PDF
    }
}
