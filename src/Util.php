<?php

namespace AAM\Payment;

class Util
{
    public static function numbersOnly(string $str): string
    {
        return preg_replace('/\D/', '', $str);
    }
}
