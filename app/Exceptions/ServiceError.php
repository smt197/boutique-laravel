<?php
namespace App\Exceptions;

use RuntimeException;

class ServiceError extends RuntimeException
{
    protected $message = 'message par defaut';

    public function __construct($message = null)
    {
        parent::__construct($message ?? $this->message);
    }
}
