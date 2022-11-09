<?php

namespace AAM\PayJunction;

use AAM\PayJunction\Rest;

class Terminal
{
    public static function getAll(Rest $rest): array
    {
        $rest->get('terminals');
        $result = $rest->getResult();

        if ($rest->isSuccess() && !empty($result['results'])) {
            return $result['results'];
        }

        return [];
    }
}
