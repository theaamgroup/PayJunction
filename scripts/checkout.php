<?php

use AAM\PayJunction\Address;
use AAM\PayJunction\Customer;
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

    $rest = useRest();

    // Create customer
    $customer = new Customer();
    $customer->setFirstName($firstName);
    $customer->setLastName($lastName);
    $rest = $customer->create($rest);
    checkRestError($rest);
    $customerId = (int) $rest->getResult()['customerId'];

    // Create address
    $address = new Address();
    $address->setAddress($billingAddress);
    $address->setCity($billingCity);
    $address->setState($billingState);
    $address->setCountry($billingCountry);
    $address->setZip($billingZip);
    $rest = $address->create($rest, $customerId);
    checkRestError($rest);
    $addressId = (int) $rest->getResult()['addressId'];

    // Create vault
    $vault = new Vault();
    $vault->setAddressId($addressId);
    $vault->setTokenId($tokenId);
    $rest = $vault->create($rest, $customerId);
    checkRestError($rest);
    $vaultId = (int) $rest->getResult()['vaultId'];

    // Create transaction
    $transaction = new Transaction();
    $transaction->setTerminalId((int) TERMINAL_ID);
    $transaction->setTokenId($tokenId);
    $transaction->setStatus('CAPTURE');
    $transaction->setBillingFirstName($firstName);
    $transaction->setBillingLastName($lastName);
    $transaction->setAmountBase($amount);
    $transaction->setBillingAddress($address);
    $transaction->setAvsCheck('ADDRESS_AND_ZIP');
    $response = $transaction->charge($rest);

    if ($response->isSuccess()) {
        http_response_code(200);
        echo json_encode($response->getResult());
    } else {
        http_response_code(400);
        echo json_encode([
            'errors' => $response->getErrorMessages(),
            'status' => $response->getCurlStatusCode()
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['errors' => [$e->getMessage()]]);
}
