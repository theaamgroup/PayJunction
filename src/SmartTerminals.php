<?php

namespace AAM\PayJunction;

class SmartTerminals
{
    public static function getAll(Rest $rest): array
    {
        $rest->get('smartterminals');
        $result = $rest->getResult();

        if ($rest->isSuccess() && !empty($result['results'])) {
            return $result['results'];
        }

        return [];
    }
}
