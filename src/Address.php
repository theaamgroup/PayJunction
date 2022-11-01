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
}
