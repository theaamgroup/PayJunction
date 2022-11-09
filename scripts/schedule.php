<?php

use AAM\PayJunction\Address;
use AAM\PayJunction\Schedule;
use AAM\PayJunction\Transaction;

require_once __DIR__ . '/functions.php';

$vaultId = 1;
$firstName = 'John';
$lastName = 'Doe';
$amount = .99;
$address = '1903 Test St.';
$city = 'Santa Barbara';
$state = 'CA';
$zip = '93101';
$country = 'US';
$startDate = new DateTime('now', new DateTimeZone('America/New_York'));
$startDate->modify('first day of next month');

$rest = useRest();
// $rest->post('schedules', [
//     'vaultId' => 1,
//     'scheduleType' => 'PERIODIC',
//     'interval' => 'MONTH',
//     'intervalCount' => 1,
//     'startDate' => $startDate->format('Y-m-d'),
//     'billingFirstName' => $firstName,
//     'billingLastName' => $lastName,
//     'amountBase' => $amount,
// ]);

// $address = new Address();
// $address->setAddress('1903 Test St.');
// $address->setCity('Santa Barbara');
// $address->setState('CA');
// $address->setZip('93101');
// $address->setCountry('US');

$schedule = new Schedule();
$schedule->setVaultId($vaultId);
$schedule->setScheduleType('PERIODIC');
$schedule->setInterval('MONTH');
$schedule->setIntervalCount(1);
$schedule->setStartDate($startDate);

$transaction = new Transaction();
$transaction->setTokenId($vaultId);
$transaction->setBillingFirstName($firstName);
$transaction->setBillingLastName($lastName);
$transaction->setAmountBase($amount);

$schedule->create($rest, $transaction);
checkRestError($rest, 'Schedule');
response($rest->getResult());
