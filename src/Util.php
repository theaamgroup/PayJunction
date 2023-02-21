<?php

namespace AAM\PayJunction;

use Exception;

class Util
{
    public static function numbersOnly(string $str): string
    {
        return preg_replace('/\D/', '', $str);
    }

    public static function alphaNumericOnly(string $str): string
    {
        return preg_replace('/[^A-Za-z0-9 ]/', '', $str);
    }

    public static function round(float $num): float
    {
        return round($num, 2);
    }

    public static function minmax(int $val, int $min, int $max = 2147483648): int
    {
        return min(max($val, $min), $max);
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

    public static function getExpMonths(): array
    {
        return [
            '1' => '01-Jan',
            '2' => '02-Feb',
            '3' => '03-Mar',
            '4' => '04-Apr',
            '5' => '05-May',
            '6' => '06-Jun',
            '7' => '07-Jul',
            '8' => '08-Aug',
            '9' => '09-Sep',
            '10' => '10-Oct',
            '11' => '11-Nov',
            '12' => '12-Dec',
        ];
    }

    public static function getExpYears(): array
    {
        $currentYear = (int) date('Y');
        $years = [$currentYear => $currentYear];

        for ($i = 1; $i <= 10; $i += 1) {
            $nextYear = $currentYear + $i;
            $years[(string) $nextYear] = $nextYear;
        }

        return $years;
    }
}
