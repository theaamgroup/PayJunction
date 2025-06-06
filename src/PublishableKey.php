<?php

namespace AAM\PayJunction;

use AAM\PayJunction\Rest;
use Exception;

class PublishableKey
{
    public static function create(Rest $rest): string
    {
        $rest->post('publishablekeys');
        $result = $rest->getResult();

        if (!$rest->isSuccess()) {
            $message = $rest->getDebugMessage() . 'Errors: ' . implode(' ', $rest->getErrorMessages());
            throw new Exception($message);
        }

        return $result['keyValue'] ?? '';
    }

    public static function getAll(Rest $rest): array
    {
        $rest->get('publishablekeys');
        $result = $rest->getResult();

        if (!$rest->isSuccess()) {
            $message = $rest->getDebugMessage() . 'Errors: ' . implode(' ', $rest->getErrorMessages());
            throw new Exception($message);
        }

        return $result['results'] ?? [];
    }

    public static function getOne(Rest $rest): string
    {
        $allKeys = self::getAll($rest);

        if (empty($allKeys)) {
            return self::create($rest);
        }

        if (!empty($allKeys[0]['keyValue'])) {
            return $allKeys[0]['keyValue'];
        }

        return '';
    }
}
