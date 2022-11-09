<?php

namespace AAM\PayJunction;

class Vault
{
    private $tokenId = '';
    private $address = '';
    private $city = '';
    private $state = '';
    private $zip = '';
    private $addressId = 0;

    public function setTokenId(string $tokenId): void
    {
        $this->tokenId = $tokenId;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address->getAddress();
        $this->city = $address->getCity();
        $this->state = $address->getState();
        $this->zip = $address->getZip();
    }

    public function setAddressId(int $addressId): void
    {
        $this->addressId = $addressId;
    }

    public static function getAll(Rest $rest, int $customerId): Rest
    {
        $rest->get("customers/$customerId/vaults");

        return $rest;
    }

    public static function get(Rest $rest, int $customerId, int $vaultId): Rest
    {
        $rest->get("customers/$customerId/vaults/$vaultId");

        return $rest;
    }

    public function create(Rest $rest, int $customerId): Rest
    {
        $rest->post("customers/$customerId/vaults", $this->getData());

        return $rest;
    }

    public function update(Rest $rest, int $customerId, int $vaultId): Rest
    {
        $rest->put("customers/$customerId/vaults/$vaultId", $this->getData());

        return $rest;
    }

    public static function delete(Rest $rest, int $customerId, int $vaultId): Rest
    {
        $rest->delete("customers/$customerId/vaults/$vaultId");

        return $rest;
    }

    public function getData(): array
    {
        return array_filter(get_object_vars($this));
    }
}
