<?php
namespace App\Services;

use Illuminate\Http\Request;
use MongoDB\Client;

interface IMongoDB
{
    public function getClient();
}
