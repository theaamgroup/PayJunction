<?php

namespace AAM\PayJunction;

use AAM\PayJunction\Rest;
use AAM\PayJunction\Level3Item;
use AAM\PayJunction\Util;

class Level3Data
{
    private $amountFreight = 0;
    private $summaryCommodityCode = '';
    private $destinationZip = '';
    private $shipFromZip = '';
    private $items = []; // 1-99 items

    public function setAmountFreight(float $amountFreight): void
    {
        $this->amountFreight = Util::round($amountFreight);
    }

    public function setSummaryCommodityCode(string $summaryCommodityCode): void
    {
        $this->summaryCommodityCode = substr(Util::alphaNumericOnly($summaryCommodityCode), 0, 4);
    }

    public function setDestinationZip(string $destinationZip): void
    {
        $this->destinationZip = substr(Util::alphaNumericOnly($destinationZip), 0, 10);
    }

    public function setShipFromZip(string $shipFromZip): void
    {
        $this->shipFromZip = substr(Util::alphaNumericOnly($shipFromZip), 0, 10);
    }

    public function addItem(Level3Item $level3Item): void
    {
        $this->items[] = $level3Item;
    }

    public function addLevel3Data(Rest $rest, string $transactionId): Rest
    {
        $data = [];

        if ($this->amountFreight) {
            $data['amountFreight'] = $this->amountFreight;
        }

        if ($this->summaryCommodityCode) {
            $data['summaryCommodityCode'] = $this->summaryCommodityCode;
        }

        if ($this->destinationZip) {
            $data['destinationZip'] = $this->destinationZip;
        }

        if ($this->shipFromZip) {
            $data['shipFromZip'] = $this->shipFromZip;
        }

        foreach ($this->items as $item) {
            $data['items'][] = $item->getData();
        }

        $rest->post(
            "transactions/$transactionId/level3",
            $data
        );

        return $rest;
    }

    public function getData(): array
    {
        return array_filter(get_object_vars($this));
    }
}
