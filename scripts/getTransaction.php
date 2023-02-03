<?php

use AAM\PayJunction\Transaction;

require_once __DIR__ . '/functions.php';

try {
    $transaction_id = (int) ($_GET['transactionId'] ?? 0);
    $rest = useRest();
    $rest = Transaction::getTransaction($rest, $transaction_id);
    response($rest->getResult(), $rest->getCurlStatusCode());
} catch (Exception $e) {
    response(['errors' => [$e->getMessage()]], 500);
}
