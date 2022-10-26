<?php

namespace AAM\Payment;

use Exception;

class Level3Item
{
    private $amountDiscount = 0;
    private $amountTax = 0;
    private $amountTotal = 0;
    private $amountUnitCost = 0;
    private $commodityCode = '';
    private $debitCreditIndicator = 'DEBIT';
    private $description = '';
    private $discountIndicator = '';
    private $discountTreatment = '';
    private $discountRate = 0;
    private $grossNetIndicator = 'GROSS';
    private $productCode = '';
    private $quantity = 1;
    private $taxRate = 0;
    private $unitOfMeasure = '';

    public function setAmountDiscount(float $amountDiscount): void
    {
        $this->amountDiscount = $amountDiscount;
    }

    public function setAmountTax(float $amountTax): void
    {
        $this->amountTax = $amountTax;
    }

    public function setAmountTotal(float $amountTotal): void
    {
        $this->amountTotal = $amountTotal;
    }

    public function setAmountUnitCost(float $amountUnitCost): void
    {
        $this->amountUnitCost = $amountUnitCost;
    }

    public function setCommodityCode(string $commodityCode): void
    {
        $this->commodityCode = substr($commodityCode, 0, 12);
    }

    public function setDebitCreditIndicator(string $debitCreditIndicator = 'DEBIT | CREDIT'): void
    {
        $debitCreditIndicator = strtoupper($debitCreditIndicator);

        if (!in_array($debitCreditIndicator, ['DEBIT', 'CREDIT'])) {
            throw new Exception('debitCreditIndicator must be DEBIT or CREDIT');
        }

        $this->debitCreditIndicator = $debitCreditIndicator;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
