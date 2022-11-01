<?php

namespace AAM\PayJunction;

class Customer
{
    private $companyName = '';
    private $custom1 = '';
    private $email = '';
    private $identifier = '';
    private $firstName = '';
    private $jobTitle = '';
    private $lastName = '';
    private $middleName = '';
    private $phone = '';
    private $phone2 = '';
    private $website = '';
    private $tokenId = '';
    private $address = '';
    private $city = '';
    private $state = '';
    private $zip = '';
    private $addressId = 0;

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = substr($companyName, 0, 64);
    }

    public function setCustom1(string $custom1): void
    {
        $this->custom1 = substr($custom1, 0, 32);
    }

    public function setEmail(string $email): void
    {
        $this->email = substr($email, 0, 128);
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = substr($identifier, 0, 64);
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = substr($firstName, 0, 16);
    }

    public function setJobTitle(string $jobTitle): void
    {
        $this->jobTitle = substr($jobTitle, 0, 32);
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = substr($lastName, 0, 32);
    }

    public function setMiddleName(string $middleName): void
    {
        $this->middleName = substr($middleName, 0, 32);
    }

    public function setPhone(string $phone): void
    {
        $this->phone = substr(Util::numbersOnly($phone), 0, 24);
    }

    public function setPhone2(string $phone2): void
    {
        $this->phone2 = substr(Util::numbersOnly($phone2), 0, 24);
    }

    public function setWebsite(string $website): void
    {
        $this->website = substr($website, 0, 128);
    }

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

    public function createCustomer(Rest $rest): Rest
    {
        $rest->post('customers', $this->getData());

        return $rest;
    }

    public function getCustomer(Rest $rest, int $customerId): Rest
    {
        $rest->get("customers/$customerId");

        return $rest;
    }

    public function updateCustomer(Rest $rest, int $customerId): Rest
    {
        $rest->put("customers/$customerId", $this->getData());

        return $rest;
    }

    public function deleteCustomer(Rest $rest, int $customerId): Rest
    {
        $rest->delete("customers/$customerId");

        return $rest;
    }

    public function createVault(Rest $rest, int $customerId): Rest
    {
        $rest->post("customers/$customerId/vaults", $this->getData());

        return $rest;
    }

    public function getVaults(Rest $rest, int $customerId): Rest
    {
        $rest->get("customers/$customerId/vaults");

        return $rest;
    }

    public function getVault(Rest $rest, int $customerId, int $vaultId): Rest
    {
        $rest->get("customers/$customerId/vaults/$vaultId");

        return $rest;
    }

    public function updateVault(Rest $rest, int $customerId, int $vaultId): Rest
    {
        $rest->put("customers/$customerId/vaults/$vaultId", $this->getData());

        return $rest;
    }

    public function deleteVault(Rest $rest, int $customerId, int $vaultId): Rest
    {
        $rest->delete("customers/$customerId/vaults/$vaultId");

        return $rest;
    }

    public function createAddress(Rest $rest, int $customerId, Address $address): Rest
    {
        $rest->post("customers/$customerId/addresses", [
            'address' => $address->getAddress(),
            'city' => $address->getCity(),
            'state' => $address->getState(),
            'country' => $address->getCountry(),
            'zip' => $address->getZip(),
        ]);

        return $rest;
    }

    public function getAddresses(Rest $rest, int $customerId): Rest
    {
        $rest->get("customers/$customerId/addresses?limit=50");

        return $rest;
    }

    public function getAddress(Rest $rest, int $customerId, int $addressId): Rest
    {
        $rest->get("customers/$customerId/addresses/$addressId");

        return $rest;
    }

    public function updateAddress(Rest $rest, int $customerId, int $addressId, Address $address): Rest
    {
        $rest->put("customers/$customerId/addresses/$addressId", [
            'address' => $address->getAddress(),
            'city' => $address->getCity(),
            'state' => $address->getState(),
            'country' => $address->getCountry(),
            'zip' => $address->getZip(),
        ]);

        return $rest;
    }

    public function deleteAddress(Rest $rest, int $customerId, int $addressId): Rest
    {
        $rest->delete("customers/$customerId/addresses/$addressId");

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