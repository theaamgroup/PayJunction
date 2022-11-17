<?php

namespace AAM\PayJunction;

use AAM\PayJunction\Rest;

class PublishableKey
{
    public static function create(Rest $rest): string
    {
        $rest->post('publishablekeys');
        $result = $rest->getResult();

        if ($rest->isSuccess() && !empty($result['keyValue'])) {
            return $result['keyValue'];
        }

        return '';
    }

    public static function getAll(Rest $rest): array
    {
        $rest->get('publishablekeys');
        $result = $rest->getResult();

        if ($rest->isSuccess() && !empty($result['results'])) {
            return $result['results'];
        }

        return [];
    }
}
