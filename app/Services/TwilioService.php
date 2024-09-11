<?php

namespace App\Services;

use App\Contracts\SmsService;
use Twilio\Rest\Client;

class TwilioService implements SmsService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    }

    /**
     * Send an SMS message.
     */
    public function sendSms($to, $message)
    {
        return $this->twilio->messages->create('+221'.$to, [
            'from' => config('services.twilio.from'),
            'body' => $message,
        ]);
    }
}
