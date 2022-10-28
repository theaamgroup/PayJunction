<?php

namespace AAM\PayJunction;

use DateTime;
use Exception;

class TransactionQuery
{
    public const STATUSES = ['CAPTURE', 'DECLINED', 'HOLD', 'REJECT', 'VOID'];

    private $status = '';
    private $settlementId = 0;
    private $terminalId = 0;
    private $scheduleId = 0;
    private $invoiceNumber = '';
    private $purchaseOrderNumber = '';
    private $amountTotal = 0;
    private $startDate = '';
    private $endDate = '';
    private $limit = 50;
    private $offset = 0;

    public function setStatus(string $status): void
    {
        $status = strtoupper($status);

        if (!in_array($status, self::STATUSES)) {
            throw new Exception('status must be one of the following: ' . implode(', ', self::STATUSES));
        }

        $this->status = $status;
    }

    public function setSettlementId(int $settlementId): void
    {
        $this->settlementId = $settlementId;
    }

    public function setTerminalId(int $terminalId): void
    {
        $this->terminalId = $terminalId;
    }

    public function setScheduleId(int $scheduleId): void
    {
        $this->scheduleId = $scheduleId;
    }

    public function setInvoiceNumber(string $invoiceNumber): void
    {
        $this->invoiceNumber = substr($invoiceNumber, 0, 32);
    }

    public function setPurchaseOrderNumber(string $purchaseOrderNumber): void
    {
        $this->purchaseOrderNumber = substr($purchaseOrderNumber, 0, 32);
    }

    public function setAmountTotal(float $amountTotal): void
    {
        $this->amountTotal = $amountTotal;
    }

    /**
     * @param DateTime $startDate - will be converted to ISO 8601 format
     */
    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate->format('c');
    }

    /**
     * @param DateTime $endDate - will be converted to ISO 8601 format
     */
    public function setEndDate(DateTime $endDate): void
    {
        $this->endDate = $endDate->format('c');
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit > 50 ? 50 : $limit;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function search(Rest $rest): Rest
    {
        $rest->get('transactions?' . http_build_query($this->getData()));

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
}
