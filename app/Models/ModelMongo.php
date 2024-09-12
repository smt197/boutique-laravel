<?php
namespace App\Models;

use MongoDB\Client as MongoClient;
use MongoDB\Collection;

class ModelMongo
{
    protected $collection;

    public function __construct()
    {
        // Configuration de la connexion MongoDB
        $client = new MongoClient('mongodb://localhost:27017'); // URL de connexion MongoDB
        $database = $client->selectDatabase('archive_db'); // Nom de votre base de données
        $this->collection = $database->selectCollection('archive_db'); // Nom de votre collection
    }

    // Exemple de méthode pour insérer un document
    public function insertDocument($data)
    {
        $result = $this->collection->insertOne($data);
        return $result->getInsertedId();
    }

    // Exemple de méthode pour trouver des documents
    public function findDocuments($filter = [])
    {
        return $this->collection->find($filter)->toArray();
    }

    // Exemple de méthode pour mettre à jour des documents
    public function updateDocument($filter, $update)
    {
        $result = $this->collection->updateMany($filter, $update);
        return $result->getModifiedCount();
    }

    // Exemple de méthode pour supprimer des documents
    public function deleteDocument($filter)
    {
        $result = $this->collection->deleteMany($filter);
        return $result->getDeletedCount();
    }
}
