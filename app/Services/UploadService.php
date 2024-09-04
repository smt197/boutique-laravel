<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Téléverse une image et retourne le chemin du fichier.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string
     */
    public function uploadImage(UploadedFile $file, $path = 'images'): string
    {
        // Générer un nom unique pour le fichier
        $filename = time() . '_' . $file->getClientOriginalName();

        // Sauvegarder le fichier dans le disque spécifié
        $filePath = $file->storeAs($path, $filename, 'public');

        // Retourner le chemin public du fichier
        return Storage::url($filePath);
    }
}
