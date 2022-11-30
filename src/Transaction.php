<?php

namespace AAM\PayJunction;

use AAM\PayJunction\Rest;
use Exception;

class Transaction
{
    public const STATUSES = ['HOLD', 'CAPTURE', 'VOID'];
    public const AVS = ['ADDRESS', 'ZIP', 'ADDRESS_AND_ZIP', 'ADDRESS_OR_ZIP', 'BYPASS', 'OFF'];

    private $tokenId = '';
    private $vaultId = 0;
    private $status = '';
    private $terminalId = '';
    private $avs = '';
    private $cvv = '';
    private $cardCvv = '';
    private $amountBase = 0;
    private $amountShipping = 0;
    private $amountTax = 0; // level 2
    private $amountFreight = 0; // level 3
    private $billingIdentifier = '';
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
    private $shippingIdentifier = '';
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

    public function setTokenId(string $tokenId): void
    {
        $this->tokenId = $tokenId;
    }

    public function setVaultId(int $vaultId): void
    {
        $this->vaultId = $vaultId;
    }

    public function setStatus(string $status = 'HOLD | CAPTURE | VOID'): void
    {
        $status = strtoupper($status);

        if (!in_array($status, self::STATUSES)) {
            throw new Exception('"status" must be HOLD, CAPTURE, or VOID');
        }

        $this->status = $status;
    }

    public function setTerminalId(int $terminalId): void
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
            throw new Exception('"avs" must be one of the following: ' . implode(', ', self::AVS));
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

    public function setBillingIdentifier(string $billingIdentifier): void
    {
        $this->billingIdentifier = substr($billingIdentifier, 0, 64);
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
            throw new Exception('"billingPhone" cannot exceed 24 characters');
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

    public function setShippingIdentifier(string $shippingIdentifier): void
    {
        $this->shippingIdentifier = substr($shippingIdentifier, 0, 64);
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
            throw new Exception('"shippingPhone" cannot exceed 24 characters');
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

    public static function getTransaction(Rest $rest, string $transactionId): Rest
    {
        $rest->get("transactions/$transactionId");

        return $rest;
    }

    public function getData(): array
    {
        return array_filter(get_object_vars($this));
    }

    private function setLevel3Eligible(Rest $rest): void
    {
        $this->level3Eligible = false;

        if ($rest->isSuccess()) {
            $result = $rest->getResult();
            $this->level3Eligible = !empty($result['response']['processor']['level3Eligible']);
        }
    }
}
