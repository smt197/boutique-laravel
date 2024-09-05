<?php
namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;

class UploadService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary();
    }

    /**
     * Téléverse une image sur Cloudinary, sinon en local en cas d'échec.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string
     */
    public function uploadImage(UploadedFile $file, $path = 'images'): string
    {
        try {
            // Tentative d'upload sur Cloudinary
            $uploadedFile = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => $path
            ]);
            return $uploadedFile['secure_url']; // URL sécurisée Cloudinary
        } catch (Exception $e) {
            // Si Cloudinary échoue, on bascule sur le stockage local
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs($path, $filename, 'public');
            return Storage::url($filePath); // URL local
        }
    }

    /**
     * Convertir une image en base64 après upload.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string
     */
    public function uploadImageAndConvertToBase64(UploadedFile $file, $path = 'images'): string
    {
        // Téléverser l'image
        $filePath = $this->uploadImage($file, $path);
        return $filePath;
        

        // Lire le contenu du fichier (en local dans ce cas)
        $fileContent = Storage::disk('public')->get($filePath);

        // Convertir le contenu en base64
        return base64_encode($fileContent);
    }
}
