<?php

use AAM\PayJunction\Rest;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

function useRest(): Rest
{
    if (!defined('APP_KEY') || !defined('API_LOGIN') || !defined('API_PASSWORD')) {
        throw new Exception('Config constants APP_KEY, API_LOGIN, and API_PASSWORD must exist');
    }

    return new Rest(APP_KEY, API_LOGIN, API_PASSWORD, true);
}

function response($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data);
    die;
}

function checkRestError(Rest $rest, string $context)
{
    if (!$rest->isSuccess()) {
        response(
            array_merge($rest->getResult(), ['context' => $context]),
            $rest->getCurlStatusCode()
        );
    }
}
