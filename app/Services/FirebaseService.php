<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $serviceAccountPath =  base_path('config/projetdette.json');

        $firebase = (new Factory)
            ->withServiceAccount($serviceAccountPath)
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->database = $firebase->createDatabase();
    }

    // Méthode pour récupérer une collection (ici une référence dans Firebase Realtime Database)
    public function getCollection(string $collectionName)
    {
        return $this->database->getReference($collectionName);
    }

    // Méthode pour récupérer un document spécifique (ici un chemin spécifique dans Firebase Realtime Database)
    public function getDocument(string $collectionName, string $documentId)
    {
        $reference = $this->database->getReference("{$collectionName}/{$documentId}");
        return $reference->getValue();
    }

    // Méthode pour sauvegarder un document (ici pour insérer des données dans Firebase Realtime Database)
    public function saveDocument(string $collectionName, string $documentId, array $data)
    {
        $reference = $this->database->getReference("{$collectionName}/{$documentId}");
        $reference->set($data);
    }
}