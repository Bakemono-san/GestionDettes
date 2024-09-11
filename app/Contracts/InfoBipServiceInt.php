<?php

namespace App\Contracts;

interface InfoBipServiceInt
{
    public function sendSms($phone, $message);
}