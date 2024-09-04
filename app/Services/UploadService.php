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

    /**
     * Convertir une image en base64 après l'avoir téléchargée.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string
     */
    public function uploadImageAndConvertToBase64(UploadedFile $file, $path = 'images'): string
    {
        // Téléverser l'image et obtenir le chemin du fichier
        $filePath = $this->uploadImage($file, $path);

        // Lire le contenu du fichier
        $fileContent = Storage::disk('public')->get($filePath);

        // Convertir le contenu du fichier en base64
        return base64_encode($fileContent);
    }
}
