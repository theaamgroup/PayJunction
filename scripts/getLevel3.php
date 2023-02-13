<?php

use AAM\PayJunction\Level3Data;

require_once __DIR__ . '/functions.php';

try {
    $transaction_id = (int) ($_GET['transactionId'] ?? 0);
    $rest = useRest();
    $rest = Level3Data::getLevel3Data($rest, (string) $transaction_id);
    response($rest->getResult(), $rest->getCurlStatusCode());
} catch (Exception $e) {
    response(['errors' => [$e->getMessage()]], 500);
}
