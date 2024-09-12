<?php
namespace App\Services;

// use App\Services\Contracts\IMongoDB;
use App\Services\IMongoDB;
use MongoDB\Client;

class IMongoImpl implements IMongoDB
{
    protected $client;
    public function __construct()
    {
        $this->client = new Client('mongodb+srv://smt197:Serigne197@cluster0.umwtoha.mongodb.net/archive_db?retryWrites=true&w=majority&appName=Cluster0');
    }

    public function getClient()
    {
        return $this->client;
    }
}