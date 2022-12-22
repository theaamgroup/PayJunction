<?php

namespace AAM\PayJunction;

use Exception;

class Webhook
{
    public const EVENTS = ['SMARTTERMINAL_REQUEST', 'TRANSACTION', 'TRANSACTION_SIGNATURE'];

    public static function create(
        Rest $rest,
        string $event = 'SMARTTERMINAL_REQUEST | TRANSACTION | TRANSACTION_SIGNATURE',
        string $url = '',
        string $secret = ''
    ): Rest {
        self::validateUrl($url);
        self::validateSecret($secret);

        if (!in_array($event, self::EVENTS)) {
            throw new Exception('"event" must be one of the following: ' . implode(', ', self::EVENTS));
        }

        $rest->post('webhooks', array_filter([
            'event' => $event,
            'url' => $url,
            'secret' => $secret,
        ]));

        return $rest;
    }

    public static function getAll(Rest $rest): array
    {
        $rest->get('webhooks');
        $result = $rest->getResult();

        if ($rest->isSuccess() && !empty($result['results'])) {
            return $result['results'];
        }

        return [];
    }

    public static function update(
        Rest $rest,
        string $webhookId,
        string $url = '',
        string $secret = ''
    ): Rest {
        self::validateUrl($url);
        self::validateSecret($secret);

        $rest->put("webhooks/$webhookId", [
            'url' => $url,
            'secret' => $secret,
        ]);

        return $rest;
    }

    private static function validateUrl(string $url = '')
    {
        if (!preg_match('/^https:\/\//', $url)) {
            throw new Exception('"url" must be HTTPS');
        }
    }

    private static function validateSecret(string $secret = '')
    {
        if (strlen($secret) > 255) {
            throw new Exception('"secret" cannot exceed 255 characters');
        }
    }

    public static function delete(Rest $rest, string $webhookId): Rest
    {
        $rest->delete("webhooks/$webhookId");

        return $rest;
    }

    public static function catchRequest(string $secret = '')
    {
        $pjSignature = $_SERVER['X-Pj-Signature'] ?? '';
        $requestBody = file_get_contents('php://input');

        if ($pjSignature && $secret) {
            $hash = hash_hmac('sha256', $requestBody, $secret);

            if (!hash_equals($pjSignature, $hash)) {
                throw new Exception('Signature does not match');
            }
        }

        $payload = json_decode($requestBody, true);

        if ($payload === null) {
            throw new Exception('Request payload cannot be decoded');
        }

        return $payload;
    }
}
