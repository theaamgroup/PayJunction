<?php

namespace AAM\PayJunction;

use Exception;

class Webhook
{
    public const EVENTS = ['SMARTTERMINAL_REQUEST', 'TRANSACTION', 'TRANSACTION_SIGNATURE'];

    public function create(
        Rest $rest,
        array $events = ['SMARTTERMINAL_REQUEST', 'TRANSACTION', 'TRANSACTION_SIGNATURE'],
        string $url = '',
        string $secret = ''
    ): Rest {
        $this->validateUrl($url);
        $this->validateSecret($secret);

        foreach ($events as $event) {
            if (!in_array($event, self::EVENTS)) {
                throw new Exception('"event" must be one of the following: ' . implode(', ', self::EVENTS));
            }
        }

        $rest->post('webhooks', array_filter([
            'event' => $events,
            'url' => $url,
            'secret' => $secret,
        ]));

        return $rest;
    }

    public static function getAll(Rest $rest): Rest
    {
        $rest->get('webhooks');

        return $rest;
    }

    public function update(
        Rest $rest,
        string $webhookId,
        string $url = '',
        string $secret = ''
    ): Rest {
        $this->validateUrl($url);
        $this->validateSecret($secret);

        $rest->put("webhooks/$webhookId", [
            'url' => $url,
            'secret' => $secret,
        ]);

        return $rest;
    }

    private function validateUrl(string $url = '')
    {
        if (!preg_match('/^https:\/\//', $url)) {
            throw new Exception('"url" must be HTTPS');
        }
    }

    private function validateSecret(string $secret = '')
    {
        if (strlen($secret) > 255) {
            throw new Exception('"secret" cannot exceed 255 characters');
        }
    }

    public function delete(Rest $rest, string $webhookId): Rest
    {
        $rest->delete("webhooks/$webhookId");

        return $rest;
    }
}
