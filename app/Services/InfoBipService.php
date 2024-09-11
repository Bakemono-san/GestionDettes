<?php

namespace App\Services;

use App\Contracts\InfoBipServiceInt;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class InfoBipService implements InfoBipServiceInt
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = config('services.infobip.base_url');
        $this->apiKey = config('services.infobip.api_key');
        $this->sender = config('services.infobip.sender');
    }

    public function sendSms($to, $message)
    {
        $url = $this->baseUrl;

        $body = [
            'messages' => [
                [
                    'from' => $this->sender,
                    'destinations' => [
                        [
                            'to' => '+221'.$to,
                        ],
                    ],
                    'text' => $message,
                ],
            ],
        ];

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => $body,
        ]);
    }
}
