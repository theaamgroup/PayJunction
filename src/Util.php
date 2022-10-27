<?php

namespace AAM\PayJunction;

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
}
