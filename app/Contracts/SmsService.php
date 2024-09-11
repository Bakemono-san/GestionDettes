<?php

namespace App\Contracts;

interface SmsService
{
    public function sendSms($phone, $message);
}