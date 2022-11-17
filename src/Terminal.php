<?php

namespace AAM\PayJunction;

use AAM\PayJunction\Rest;
use Exception;

class Terminal
{
    public const TYPES = ['CARD', 'ACH'];

    public static function getAll(Rest $rest, string $type = 'CARD | ACH'): array
    {
        $rest->get('terminals');
        $result = $rest->getResult();

        if (!$rest->isSuccess() || empty($result['results'])) {
            return [];
        }

        if (!in_array($type, self::TYPES)) {
            return $result['results'];
        }

        return array_filter($result['results'], function ($item) use ($type) {
            return strtoupper($type) === $item['type'];
        });
    }

    public static function getTerminalId(Rest $rest, string $nickName, string $type = 'CARD | ACH')
    {
        foreach (self::getAll($rest, $type) as $terminal) {
            if (isset($terminal['nickName']) && $terminal['nickName'] === $nickName) {
                return (int) $terminal['terminalId'];
            }
        }

        throw new Exception("No terminal found by nickName: $nickName");
    }
}
