<?php

namespace AAM\PayJunction;

use AAM\PayJunction\Rest;
use Exception;

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

    public static function getTerminalId(Rest $rest, string $nickName)
    {
        foreach (self::getAll($rest) as $terminal) {
            if (isset($terminal['nickName']) && $terminal['nickName'] === $nickName) {
                return (int) $terminal['terminalId'];
            }
        }

        throw new Exception("No terminal found by nickName: $nickName");
    }
}
