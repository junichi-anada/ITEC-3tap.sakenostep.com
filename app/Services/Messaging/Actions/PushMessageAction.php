<?php

namespace App\Services\Messaging\Actions;

use GuzzleHttp\Client;

class PushMessageAction
{
    private $httpClient;
    private $channelAccessToken;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->channelAccessToken = config('services.line.channel_token');
    }

    public function sendMessage($userId, $message)
    {
        $response = $this->httpClient->post('https://api.line.me/v2/bot/message/push', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->channelAccessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'to' => $userId,
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => $message,
                    ],
                ],
            ],
        ]);

        return $response->getStatusCode() === 200;
    }
}
