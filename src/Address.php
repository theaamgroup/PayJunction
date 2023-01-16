<?php

namespace AAM\PayJunction;

class Address
{
    private $address = '';
    private $city = '';
    private $state = '';
    private $country = '';
    private $zip = '';

    public function setAddress(string $address): void
    {
        $this->address = substr($address, 0, 128);
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setCity(string $city): void
    {
        $this->city = substr($city, 0, 32);
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setState(string $state): void
    {
        $this->state = substr($state, 0, 32);
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setCountry(string $country): void
    {
        $this->country = substr($country, 0, 32);
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setZip(string $zip): void
    {
        $this->zip = substr($zip, 0, 12);
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public static function getAll(Rest $rest, int $customerId): Rest
    {
        $rest->get("customers/$customerId/addresses?limit=50");

        return $rest;
    }

    public static function get(Rest $rest, int $customerId, int $addressId): Rest
    {
        $rest->get("customers/$customerId/addresses/$addressId");

        return $rest;
    }

    public function create(Rest $rest, int $customerId): Rest
    {
        $rest->post("customers/$customerId/addresses", $this->getData());

        return $rest;
    }

    public function update(Rest $rest, int $customerId, int $addressId): Rest
    {
        $rest->put("customers/$customerId/addresses/$addressId", $this->getData());

        return $rest;
    }

    /**
     * Updates a customer address if it exists;
     * otherwise, creates a new customer address.
     */
    public static function updateOrCreate(Rest $rest, int $customerId, Address $address): Rest
    {
        // Search for address
        $address_results = self::getAll($rest, $customerId)->getResult('results');
        $existing = null;

        if (is_array($address_results) && $address->getAddress()) {
            foreach ($address_results as $address_result) {
                $addr1 = strtolower($address->getAddress());
                $addr2 = strtolower($address_result['address'] ?? '');
                $zip1 = strtolower($address->getZip());
                $zip2 = strtolower($address_result['zip'] ?? '');
                $addr_match = strpos($addr1, $addr2) !== false || strpos($addr2, $addr1) !== false;
                $zip_match = $zip1 === $zip2;

                if ($addr_match && $zip_match) {
                    $existing = $address_result;
                }
            }
        }

        // Update existing address
        if (isset($existing['addressId'])) {
            return $address->update($rest, $customerId, (int) $existing['addressId']);
        }

        return $address->create($rest, $customerId);
    }

    public static function delete(Rest $rest, int $customerId, int $addressId): Rest
    {
        $rest->delete("customers/$customerId/addresses/$addressId");

        return $rest;
    }

    public function getData(): array
    {
        return array_filter(get_object_vars($this));
    }
}
