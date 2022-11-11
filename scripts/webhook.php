<?php

use AAM\PayJunction\Webhook;

require_once __DIR__ . '/functions.php';

try {
    $rest = useRest();
    $url = 'https://testpj.requestcatcher.com/test';
    echo '<pre>';
    $rest = Webhook::create($rest, 'SMARTTERMINAL_REQUEST', $url, WEBHOOK_SECRET);
    print_r($rest->getResult());
    $rest = Webhook::create($rest, 'TRANSACTION', $url, WEBHOOK_SECRET);
    print_r($rest->getResult());
    $rest = Webhook::create($rest, 'TRANSACTION_SIGNATURE', $url, WEBHOOK_SECRET);
    print_r($rest->getResult());
    echo '</pre>';
} catch (Exception $e) {
    response(['errors' => [$e->getMessage()]], $rest->getCurlStatusCode());
}
