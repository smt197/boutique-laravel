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
        $this->client = new Client(env('MONGO_DB_CONNECTION_STRING'));
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}