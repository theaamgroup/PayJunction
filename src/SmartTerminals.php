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

    public static function getSmartTerminalId(Rest $rest, string $nickName)
    {
        foreach (self::getAll($rest) as $smartTerminal) {
            if (isset($smartTerminal['nickName']) && $smartTerminal['nickName'] === $nickName) {
                return (int) $smartTerminal['smartTerminalId'];
            }
        }

        throw new Exception("No smart terminal found by nickName: $nickName");
    }
}
