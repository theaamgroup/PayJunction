<?php

use AAM\PayJunction\Rest;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

function useRest(): Rest
{
    if (!defined('APP_KEY') || !defined('API_LOGIN') || !defined('API_PASSWORD')) {
        throw new Exception('Config constants APP_KEY, API_LOGIN, and API_PASSWORD must exist');
    }

    $rest = new Rest(APP_KEY, API_LOGIN, API_PASSWORD, SANDBOX);
    $rest->setApiVersion('2023-05-16');
    return $rest;
}

function response($data, $status = 200)
{
    header('Content-Type: application/json');
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
