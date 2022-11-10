<?php

namespace AAM\PayJunction;

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
    private $discountIndicator = ''; // set by setAmountDiscount
    private $discountTreatment = ''; // set by setAmountDiscount
    private $discountRate = 0;
    private $grossNetIndicator = 'GROSS';
    private $productCode = '';
    private $quantity = 1;
    private $taxRate = 0;
    private $unitOfMeasure = '';

    public function setAmountDiscount(float $amountDiscount): void
    {
        $this->amountDiscount = Util::round($amountDiscount);

        if ($this->amountDiscount) {
            $this->discountIndicator = 'DISCOUNTED';
            $this->discountTreatment = 'TAX_PRE_DISCOUNT';
        } else {
            $this->discountIndicator = 'NOT_DISCOUNTED';
            $this->discountTreatment = '';
        }
    }

    public function setAmountTax(float $amountTax): void
    {
        $this->amountTax = Util::round($amountTax);
    }

    public function setAmountTotal(float $amountTotal): void
    {
        $this->amountTotal = Util::round($amountTotal);
    }

    public function setAmountUnitCost(float $amountUnitCost): void
    {
        $this->amountUnitCost = Util::round($amountUnitCost);
    }

    public function setCommodityCode(string $commodityCode): void
    {
        $this->commodityCode = substr($commodityCode, 0, 12);
    }

    public function setDebitCreditIndicator(string $debitCreditIndicator = 'DEBIT | CREDIT'): void
    {
        $debitCreditIndicator = strtoupper($debitCreditIndicator);

        if (!in_array($debitCreditIndicator, ['DEBIT', 'CREDIT'])) {
            throw new Exception('"debitCreditIndicator" must be DEBIT or CREDIT');
        }

        $this->debitCreditIndicator = $debitCreditIndicator;
    }

    public function setDescription(string $description): void
    {
        $this->description = substr($description, 0, 35);
    }

    public function setDiscountRate(float $discountRate): void
    {
        $this->discountRate = round($discountRate, 2);
    }

    /**
     * @param string $grossNetIndicator = GROSS (default) | NET
     * GROSS - amountTotal includes tax (i.e. the total price paid for the item)
     * NET - amountTotal does not include tax (i.e. the pre-tax price)
     */
    public function setGrossNetIndicator(string $grossNetIndicator = 'GROSS | NET'): void
    {
        $grossNetIndicator = strtoupper($grossNetIndicator);

        if (!in_array($grossNetIndicator, ['GROSS', 'NET'])) {
            throw new Exception('"grossNetIndicator" must be GROSS or NET');
        }

        $this->grossNetIndicator = $grossNetIndicator;
    }

    public function setProductCode(string $productCode): void
    {
        $this->productCode = substr($productCode, 0, 12);
    }

    public function setQuantity(float $quantity): void
    {
        $this->quantity = Util::round($quantity);
    }

    public function setTaxRate(float $taxRate): void
    {
        $this->taxRate = round($taxRate, 2);
    }

    public function setUnitOfMeasure(string $unitOfMeasure): void
    {
        $this->unitOfMeasure = substr($unitOfMeasure, 0, 12);
    }

    public function getData(): array
    {
        return array_filter(get_object_vars($this));
    }
}
