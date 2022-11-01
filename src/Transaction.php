<?php

namespace AAM\PayJunction;

use AAM\PayJunction\Rest;
use DateTime;
use Exception;

class Transaction
{
    public const STATUSES = ['HOLD', 'CAPTURE', 'VOID'];
    public const AVS = ['ADDRESS', 'ZIP', 'ADDRESS_AND_ZIP', 'ADDRESS_OR_ZIP', 'BYPASS', 'OFF'];
    public const SCHEDULE_TYPES = ['PERIODIC', 'SPECIFIC_DATES'];
    public const SCHEDULE_INTERVALS = ['DAY', 'MONTH', 'WEEK', 'YEAR'];

    private $tokenId = '';
    private $status = '';
    private $terminalId = '';
    private $avs = 'OFF';
    private $cvv = 'OFF';
    private $cardCvv = '';
    private $amountBase = 0;
    private $amountShipping = 0;
    private $amountTax = 0; // level 2
    private $amountFreight = 0; // level 3
    private $billingFirstName = '';
    private $billingLastName = '';
    private $billingCompanyName = ''; // level 2
    private $billingPhone = '';
    private $billingAddress = ''; // level 2
    private $billingCity = '';
    private $billingState = '';
    private $billingZip = ''; // level 2
    private $billingCountry = '';
    private $billingEmail = '';
    private $shippingFirstName = '';
    private $shippingLastName = '';
    private $shippingCompanyName = '';
    private $shippingPhone = '';
    private $shippingAddress = '';
    private $shippingCity = '';
    private $shippingState = '';
    private $shippingZip = '';
    private $shippingCountry = '';
    private $shippingEmail = '';
    private $invoiceNumber = '';
    private $purchaseOrderNumber = ''; // level 2
    private $note = '';
    private $level3Eligible = false;

    // Schedule fields
    private $vaultId = '';
    private $transactionId = '';
    private $scheduleType = '';
    private $interval = '';
    private $intervalCount = 0;
    private $intervalCap = 0;
    private $startDate = '';
    private $specificDate1 = '';

    public function setTokenId(string $tokenId): void
    {
        $this->tokenId = $tokenId;
    }

    public function setStatus(string $status = 'HOLD | CAPTURE | VOID'): void
    {
        $status = strtoupper($status);

        if (!in_array($status, self::STATUSES)) {
            throw new Exception('Status must be HOLD, CAPTURE, or VOID');
        }

        $this->status = $status;
    }

    public function setTerminalId(string $terminalId): void
    {
        $this->terminalId = $terminalId;
    }

    /**
     * @param string $avs = ADDRESS | ZIP | ADDRESS_AND_ZIP | ADDRESS_OR_ZIP | BYPASS | OFF
     * ADDRESS - Decline if address does not match.
     * ZIP - Decline if zip does not match.
     * ADDRESS_AND_ZIP - Decline if address and zip don't match.
     * ADDRESS_OR_ZIP - Decline if address or zip don't match.
     * BYPASS - Try to match address and zip but do not decline if one does not match.
     * OFF - Do not run AVS
     */
    public function setAvsCheck(string $avs = 'ADDRESS | ZIP | ADDRESS_AND_ZIP | ADDRESS_OR_ZIP | BYPASS | OFF'): void
    {
        $avs = strtoupper($avs);

        if (!in_array($avs, self::AVS)) {
            throw new Exception('AVS must be one of the following: ' . implode(', ', self::AVS));
        }

        $this->avs = $avs;
    }

    public function setCvvCheck(bool $cvv): void
    {
        $this->cvv = $cvv ? 'ON' : 'OFF';
    }

    public function setCvv(string $cardCvv): void
    {
        $this->cardCvv = $cardCvv;
    }

    public function setAmountBase(float $amountBase): void
    {
        $this->amountBase = Util::round($amountBase);
    }

    public function setAmountShipping(float $amountShipping): void
    {
        $this->amountShipping = Util::round($amountShipping);
    }

    public function setAmountTax(float $amountTax): void
    {
        $this->amountTax = Util::round($amountTax);
    }

    public function setBillingFirstName(string $billingFirstName): void
    {
        $this->billingFirstName = substr($billingFirstName, 0, 16);
    }

    public function setBillingLastName(string $billingLastName): void
    {
        $this->billingLastName = substr($billingLastName, 0, 32);
    }

    public function setBillingCompanyName(string $billingCompanyName): void
    {
        $this->billingCompanyName = substr($billingCompanyName, 0, 64);
    }

    public function setBillingPhone(string $billingPhone): void
    {
        $raw_phone = Util::numbersOnly($billingPhone);

        if (strlen($raw_phone) > 24) {
            throw new Exception('Billing phone cannot exceed 24 characters');
        }

        $this->billingPhone = $raw_phone;
    }

    public function setBillingAddress(Address $address): void
    {
        $this->billingAddress = $address->getAddress();
        $this->billingCity = $address->getCity();
        $this->billingState = $address->getState();
        $this->billingCountry = $address->getCountry();
        $this->billingZip = $address->getZip();
    }

    public function setBillingEmail(string $billingEmail): void
    {
        $this->billingEmail = substr($billingEmail, 0, 128);
    }

    public function setShippingFirstName(string $shippingFirstName): void
    {
        $this->shippingFirstName = substr($shippingFirstName, 0, 16);
    }

    public function setShippingLastName(string $shippingLastName): void
    {
        $this->shippingLastName = substr($shippingLastName, 0, 32);
    }

    public function setShippingCompanyName(string $shippingCompanyName): void
    {
        $this->shippingCompanyName = substr($shippingCompanyName, 0, 64);
    }

    public function setShippingPhone(string $shippingPhone): void
    {
        $raw_phone = Util::numbersOnly($shippingPhone);

        if (strlen($raw_phone) > 24) {
            throw new Exception('Shipping phone cannot exceed 24 characters');
        }

        $this->shippingPhone = $raw_phone;
    }

    public function setShippingAddress(Address $address): void
    {
        $this->shippingAddress = $address->getAddress();
        $this->shippingCity = $address->getCity();
        $this->shippingState = $address->getState();
        $this->shippingCountry = $address->getCountry();
        $this->shippingZip = $address->getZip();
    }

    public function setShippingEmail(string $shippingEmail): void
    {
        $this->shippingEmail = substr($shippingEmail, 0, 128);
    }

    public function setInvoiceNumber(string $invoiceNumber): void
    {
        $this->invoiceNumber = substr($invoiceNumber, 0, 32);
    }

    public function setPurchaseOrderNumber(string $purchaseOrderNumber): void
    {
        $this->purchaseOrderNumber = substr($purchaseOrderNumber, 0, 32);
    }

    public function setNote(string $note): void
    {
        $this->note = substr($note, 0, 2048);
    }

    public function level3Eligible(): bool
    {
        return $this->level3Eligible;
    }

    public function charge(Rest $rest): Rest
    {
        $rest->post(
            'transactions',
            array_merge(['action' => 'CHARGE'], $this->getData())
        );

        $this->setLevel3Eligible($rest);

        return $rest;
    }

    public function refund(Rest $rest): Rest
    {
        $rest->post(
            'transactions',
            array_merge(['action' => 'REFUND'], $this->getData())
        );

        $this->setLevel3Eligible($rest);

        return $rest;
    }

    public function verify(Rest $rest): Rest
    {
        $rest->post(
            'transactions',
            array_merge(['action' => 'VERIFY'], $this->getData())
        );

        $this->setLevel3Eligible($rest);

        return $rest;
    }

    public function getTransaction(Rest $rest, string $transactionId): Rest
    {
        $rest->get("transactions/$transactionId");

        return $rest;
    }

    private function getData(): array
    {
        $data = [];

        foreach (get_object_vars($this) as $key => $val) {
            if ($val) {
                $data[$key] = $val;
            }
        }

        return $data;
    }

    private function setLevel3Eligible(Rest $rest): void
    {
        $this->level3Eligible = false;

        if ($rest->isSuccess()) {
            $result = $rest->getResult();
            $this->level3Eligible = !empty($result['response']['processor']['level3Eligible']);
        }
    }

    /**
     * Used for Schedules
     * @param string $vaultId - A Vault ID from a previously stored account.
     */
    public function setVaultId(string $vaultId): void
    {
        $this->vaultId = $vaultId;
    }

    /**
     * Used for Schedules
     * @param string $transactionId - A Transaction ID from a previously charged transaction.
     */
    public function setTransactionId(string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    public function setScheduleType(string $scheduleType = 'PERIODIC | SPECIFIC_DATES'): void
    {
        $scheduleType = strtoupper($scheduleType);

        if (!in_array($scheduleType, self::SCHEDULE_TYPES)) {
            throw new Exception('scheduleType must be one of the following: ' . implode(', ', self::SCHEDULE_TYPES));
        }

        $this->scheduleType = $scheduleType;
    }

    /**
     * Required for scheduleType=PERIODIC
     */
    public function setInterval(string $interval = 'DAY | MONTH | WEEK | YEAR'): void
    {
        $interval = strtoupper($interval);

        if (!in_array($interval, self::SCHEDULE_INTERVALS)) {
            throw new Exception('interval must be one of the following: ' . implode(', ', self::SCHEDULE_INTERVALS));
        }

        $this->interval = $interval;
    }

    /**
     * Required for scheduleType=PERIODIC
     */
    public function setIntervalCount(int $intervalCount): void
    {
        $this->intervalCount = $intervalCount;
    }

    /**
     * Required for scheduleType=PERIODIC
     * If present, schedule will only run until this number of approved transactions is reached.
     */
    public function setIntervalCap(int $intervalCap): void
    {
        $this->intervalCap = $intervalCap;
    }

    /**
     * Required for scheduleType=PERIODIC
     */
    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate->format('Y-m-d');
    }

    /**
     * Required for scheduleType=SPECIFIC_DATES
     */
    public function setSpecificDate(DateTime $specificDate1): void
    {
        $this->specificDate1 = $specificDate1->format('Y-m-d');
    }
}
