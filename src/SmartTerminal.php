<?php

namespace AAM\PayJunction;

use Exception;

class SmartTerminal
{
    public const INPUT_TYPES = ['PHONE'];
    public const ACTIONS = ['CHARGE', 'REFUND'];

    private $action = '';
    private $amountBase = 0;
    private $amountShipping = 0;
    private $terminalId = 0;
    private $showReceiptPrompt = false;
    private $showSignaturePrompt = true;
    private $allowTips = false;
    private $keyed = 'ALLOW';
    private $invoiceNumber = 0;
    private $purchaseOrderNumber = '';
    private $billingIdentifier = '';
    private $billingEmail = '';
    private $shippingIdentifier = '';
    private $shippingEmail = '';
    private $note = '';

    public function setAction(string $action = 'CHARGE | REFUND'): void
    {
        $action = strtoupper($action);

        if (!in_array($action, self::ACTIONS)) {
            throw new Exception('"action" must be one of the following: ' . implode(', ', self::ACTIONS));
        }

        $this->action = $action;
    }

    public function setAmountBase(float $amountBase): void
    {
        $amountBase = round($amountBase, 2);

        if ($amountBase < 0 || $amountBase > 1000000) {
            throw new Exception('"amountBase" must be between 0 and 1,000,000');
        }

        $this->amountBase = $amountBase;
    }

    public function setAmountShipping(float $amountShipping): void
    {
        $amountShipping = round($amountShipping, 2);

        if ($amountShipping < 0 || $amountShipping > 1000000) {
            throw new Exception('"amountShipping" must be between 0 and 1,000,000');
        }

        $this->amountShipping = $amountShipping;
    }

    public function setTerminalId(int $terminalId): void
    {
        $this->terminalId = $terminalId;
    }

    public function setShowReceiptPrompt(bool $showReceiptPrompt): void
    {
        $this->showReceiptPrompt = $showReceiptPrompt;
    }

    public function setShowSignaturePrompt(bool $showSignaturePrompt): void
    {
        $this->showSignaturePrompt = $showSignaturePrompt;
    }

    public function setInvoiceNumber(int $invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function setPurchaseOrderNumber(string $purchaseOrderNumber): void
    {
        $this->purchaseOrderNumber = substr($purchaseOrderNumber, 0, 32);
    }

    public function setBillingIdentifier(string $billingIdentifier): void
    {
        $this->billingIdentifier = substr($billingIdentifier, 0, 64);
    }

    public function setBillingEmail(string $billingEmail): void
    {
        $this->billingEmail = strtolower(substr($billingEmail, 0, 125));
    }

    public function setShippingIdentifier(string $shippingIdentifier): void
    {
        $this->shippingIdentifier = substr($shippingIdentifier, 0, 64);
    }

    public function setShippingEmail(string $shippingEmail): void
    {
        $this->shippingEmail = strtolower(substr($shippingEmail, 0, 128));
    }

    public function setNote(string $note): void
    {
        $this->note = substr($note, 0, 2048);
    }

    public static function getAll(Rest $rest): array
    {
        $rest->get('smartterminals');
        $result = $rest->getResult();

        if ($rest->isSuccess() && !empty($result['results'])) {
            return $result['results'];
        }

        return [];
    }

    public static function getSmartTerminalId(Rest $rest, string $nickName)
    {
        foreach (self::getAll($rest) as $smartTerminal) {
            if (isset($smartTerminal['nickName']) && $smartTerminal['nickName'] === $nickName) {
                return $smartTerminal['smartTerminalId'];
            }
        }

        throw new Exception("No smart terminal found by nickName: $nickName");
    }

    /**
     * Collect customer input via the Smart Terminal, such as their phone number.
     */
    public static function requestInput(
        Rest $rest,
        string $smartTerminalId,
        int $terminalId,
        string $inputType = 'PHONE'
    ): Rest {
        $inputType = strtoupper($inputType);

        if (!in_array($inputType, self::INPUT_TYPES)) {
            throw new Exception('"inputType" must be one of the following: ' . implode(', ', self::INPUT_TYPES));
        }

        $rest->post("smartterminals/$smartTerminalId/request-input", [
            'terminalId' => $terminalId,
            'type' => $inputType,
        ]);

        return $rest;
    }

    /**
     * Initiate a transaction on the Smart Terminal. The Smart Terminal will automatically
     * determine if the customer needs to insert the chip card, swipe the card,
     * enter a PIN or collect a signature.
     */
    public function requestPayment(Rest $rest, string $smartTerminalId): Rest
    {
        $rest->post("smartterminals/$smartTerminalId/request-payment", $this->getData());

        return $rest;
    }

    public function getData(): array
    {
        return array_filter(get_object_vars($this));
    }

    /**
     * Returns the Smart Terminal to the main "Smart Terminal by PayJunction"
     * screen to await a new transaction request.
     */
    public static function main(Rest $rest, string $smartTerminalId): Rest
    {
        $rest->post("smartterminals/$smartTerminalId/main");

        return $rest;
    }
}
