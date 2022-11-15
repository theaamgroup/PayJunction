<?php

use AAM\PayJunction\SmartTerminal;
use AAM\PayJunction\Terminal;

require_once __DIR__ . '/functions.php';

try {
    $rest = useRest();
    $st = new SmartTerminal();
    $st->setInvoiceNumber(time()); // bypass duplicate transaction
    $st->setAction('CHARGE');
    $st->setAmountBase(1.00);
    $st->setTerminalId(Terminal::getTerminalId($rest, 'Labs Account'));
    $st->setShowReceiptPrompt(true);
    $st->setShowSignaturePrompt(true);
    $st->setNote('SMART TERMINAL TEST');
    $st->requestPayment($rest, SmartTerminal::getSmartTerminalId($rest, 'Labs Terminal'));
    response($rest->getResult(), $rest->getCurlStatusCode());
} catch (Exception $e) {
    response(['errors' => [$e->getMessage()]], 500);
}
