<?php

namespace AAM\Payment;

use AAM\Payment\Api\Client\Rest;
use Exception;

class Card
{
    public const STATUSES = ['HOLD', 'CAPTURE', 'VOID'];

    private $tokenId = '';
    private $status = '';
    private $terminalId = '';
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
    private $summaryCommodityCode = ''; // level 3
    private $destinationZip = ''; // level 3
    private $shipFromZip = ''; // level 3
    private $items = []; // level 3 (1-99 items)

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

    public function setAmountBase(float $amountBase): void
    {
        $this->amountBase = $amountBase;
    }

    public function setAmountShipping(float $amountShipping): void
    {
        $this->amountShipping = $amountShipping;
    }

    public function setAmountTax(float $amountTax): void
    {
        $this->amountTax = $amountTax;
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

    public function setBillingAddress(string $billingAddress): void
    {
        $this->billingAddress = substr($billingAddress, 0, 128);
    }

    public function setBillingCity(string $billingCity): void
    {
        $this->billingCity = substr($billingCity, 0, 32);
    }

    public function setBillingState(string $billingState): void
    {
        $this->billingState = substr($billingState, 0, 32);
    }

    public function setBillingZip(string $billingZip): void
    {
        $this->billingZip = substr($billingZip, 0, 12);
    }

    public function setBillingCountry(string $billingCountry): void
    {
        $this->billingCountry = substr($billingCountry, 0, 32);
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

    public function setShippingAddress(string $shippingAddress): void
    {
        $this->shippingAddress = substr($shippingAddress, 0, 128);
    }

    public function setShippingCity(string $shippingCity): void
    {
        $this->shippingCity = substr($shippingCity, 0, 32);
    }

    public function setShippingState(string $shippingState): void
    {
        $this->shippingState = substr($shippingState, 0, 32);
    }

    public function setShippingZip(string $shippingZip): void
    {
        $this->shippingZip = substr($shippingZip, 0, 12);
    }

    public function setShippingCountry(string $shippingCountry): void
    {
        $this->shippingCountry = substr($shippingCountry, 0, 32);
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
        $rest->call(
            'POST',
            'transactions',
            array_merge(['action' => 'CHARGE'], $this->buildData())
        );

        $this->setLevel3Eligible($rest);

        return $rest;
    }

    public function refund(Rest $rest): Rest
    {
        $rest->call(
            'POST',
            'transactions',
            array_merge(['action' => 'REFUND'], $this->buildData())
        );

        $this->setLevel3Eligible($rest);

        return $rest;
    }

    public function verify(Rest $rest): Rest
    {
        $rest->call(
            'POST',
            'transactions',
            array_merge(['action' => 'VERIFY'], $this->buildData())
        );

        $this->setLevel3Eligible($rest);

        return $rest;
    }

    private function buildData(): array
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
}
