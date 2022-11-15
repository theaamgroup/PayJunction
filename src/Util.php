<?php

namespace AAM\PayJunction;

use Exception;

class Util
{
    public static function numbersOnly(string $str): string
    {
        return preg_replace('/\D/', '', $str);
    }

    public static function round(float $num): float
    {
        return round($num, 4);
    }

    public static function generateSecret(int $length = 16): string
    {
        if ($length < 16) {
            throw new Exception('"length" cannot be less than 16');
        }

        if ($length > 255) {
            throw new Exception('"length" cannot be more than 255');
        }

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
