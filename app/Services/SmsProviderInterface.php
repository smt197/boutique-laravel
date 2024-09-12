<?php

namespace App\Services;

interface SmsProviderInterface
{
    public function sendSms(string $to, string $message): void;
}
