<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ClientServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ClientService';
    }
}
