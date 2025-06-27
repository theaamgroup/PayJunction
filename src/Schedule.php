<?php

namespace AAM\PayJunction;

use DateTime;

class Schedule
{
    public const SCHEDULE_TYPES = ['PERIODIC', 'SPECIFIC_DATES'];
    public const SCHEDULE_INTERVALS = ['DAY', 'MONTH', 'WEEK', 'YEAR'];

    private $vaultId = 0;
    private $transactionId = 0;
    private $scheduleType = '';
    private $interval = '';
    private $intervalCount = 0;
    private $intervalCap = 0;
    private $startDate = '';
    private $specificDate1 = '';
    private $bypassDuplicate = 'true';

    public function setVaultId(int $vaultId): void
    {
        $this->vaultId = $vaultId;
    }

    public function setTransactionId(int $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    public function setScheduleType(string $scheduleType = 'PERIODIC | SPECIFIC_DATES'): void
    {
        $scheduleType = strtoupper($scheduleType);

        if (!in_array($scheduleType, self::SCHEDULE_TYPES)) {
            throw new Exception('"scheduleType" must be one of the following: ' . implode(', ', self::SCHEDULE_TYPES));
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
            throw new Exception('"interval" must be one of the following: ' . implode(', ', self::SCHEDULE_INTERVALS));
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

    public function setBypassDuplicate(bool $bypassDuplicate): void
    {
        $this->bypassDuplicate = $bypassDuplicate ? 'true' : 'false';
    }

    public function create(Rest $rest, Transaction $transaction): Rest
    {
        $rest->post(
            'schedules',
            array_merge(
                $transaction->getData(),
                $this->getData()
            )
        );

        return $rest;
    }

    public function getData(): array
    {
        return array_filter(get_object_vars($this));
    }
}
