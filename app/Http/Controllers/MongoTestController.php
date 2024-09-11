<?php

namespace App\Http\Controllers;

use App\Services\IMongoDB;
use Illuminate\Http\Request;

class MongoTestController extends Controller
{
    protected $mongoClient;

    public function __construct(IMongoDB $mongoConnection)
    {
        $this->mongoClient = $mongoConnection->getClient();
    }

    public function testConnection()
    {
        try {
            // Liste des bases de données
            $databases = $this->mongoClient->listDatabases();

            // Extraction des noms de bases de données
            $databaseNames = [];
            foreach ($databases as $database) {
                $databaseNames[] = $database->getName();
            }

            return response()->json([
                'status' => 'success',
                'databases' => $databaseNames
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}