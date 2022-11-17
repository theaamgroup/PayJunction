<?php

use AAM\PayJunction\Address;
use AAM\PayJunction\Customer;
use AAM\PayJunction\Schedule;
use AAM\PayJunction\Terminal;
use AAM\PayJunction\Transaction;
use AAM\PayJunction\Vault;

require_once __DIR__ . '/functions.php';

try {
    $tokenId = $_POST['tokenId'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $billingAddress = $_POST['billingAddress'];
    $billingCity = $_POST['billingCity'];
    $billingState = $_POST['billingState'];
    $billingCountry = $_POST['billingCountry'];
    $billingZip = $_POST['billingZip'];
    $amount = (float) $_POST['amount'];
    $recurringAmount = (float) $_POST['recurringAmount'];

    $rest = useRest();

    // Create customer
    $customer = new Customer();
    $customer->setFirstName($firstName);
    $customer->setLastName($lastName);
    $rest = $customer->create($rest);
    checkRestError($rest, 'Customer');
    $customerId = (int) $rest->getResult('customerId');

    // Create address
    $address = new Address();
    $address->setAddress($billingAddress);
    $address->setCity($billingCity);
    $address->setState($billingState);
    $address->setCountry($billingCountry);
    $address->setZip($billingZip);
    $rest = $address->create($rest, $customerId);
    checkRestError($rest, 'Address');
    $addressId = (int) $rest->getResult('addressId');

    // Create vault
    $vault = new Vault();
    $vault->setAddressId($addressId);
    $vault->setTokenId($tokenId);
    $rest = $vault->create($rest, $customerId);
    checkRestError($rest, 'Vault');
    $vaultId = (int) $rest->getResult('vaultId');

    // Create transaction
    $transaction = new Transaction();
    $transaction->setInvoiceNumber(time()); // bypass duplicate transaction blocking
    $transaction->setVaultId($vaultId);
    $transaction->setTerminalId(Terminal::getTerminalId($rest, 'Labs Account', 'CARD'));
    $transaction->setStatus('CAPTURE');
    $transaction->setBillingFirstName($firstName);
    $transaction->setBillingLastName($lastName);
    $transaction->setAmountBase($amount);
    $transaction->setBillingAddress($address);
    $transaction->setAvsCheck('ADDRESS_AND_ZIP');

    if ($amount > 0) {
        $rest = $transaction->charge($rest);
    } else {
        $rest = $transaction->verify($rest);
    }

    checkRestError($rest, 'Transaction');
    $transactionId = (int) $rest->getResult('transactionId');

    // Create schedule
    if ($transactionId && $recurringAmount) {
        $transaction->setInvoiceNumber('');
        $transaction->setAmountBase($recurringAmount);
        $schedule = new Schedule();
        $schedule->setScheduleType('PERIODIC');
        $schedule->setInterval('MONTH');
        $schedule->setIntervalCount(1);
        $startDate = new DateTime('now', new DateTimeZone('America/New_York'));
        $startDate->modify('first day of next month');
        $schedule->setStartDate($startDate);
        $schedule->create($rest, $transaction);
        checkRestError($rest, 'Schedule');
    }

    response($rest->getResult());
} catch (Exception $e) {
    response(['errors' => [$e->getMessage()]], 500);
}
