<?php

namespace AAM\PayJunction;

use Exception;

class SmartTerminal
{
    public const INPUT_TYPES = ['PHONE'];

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

    public function requestInput(
        Rest $rest,
        string $smartTerminalId,
        int $terminalId,
        string $inputType = 'PHONE'
    ): Rest {
        $inputType = strtoupper($inputType);

        if (!in_array($inputType, self::INPUT_TYPES)) {
            throw new Exception('"inputType" must be one of the following: ' . implode(', ', self::INPUT_TYPES));
        }

        $rest->post("smartterminals/$smartTerminalId/request-input", [
            'terminalId' => $terminalId,
            'type' => $inputType,
        ]);

        return $rest;
    }
}
