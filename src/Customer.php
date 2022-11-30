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
    private $limit = 50;
    private $offset = 0;

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

    public function setLimit(int $limit): void
    {
        $this->limit = Util::minmax($limit, 1, 50);
    }

    public function setOffset(int $offset): void
    {
        $this->offset = Util::minmax($offset, 0);
    }

    public function isSearchable(): bool
    {
        $searchableFields = array_filter($this->getData(), function ($k) {
            return in_array($k, ['firstName', 'lastName', 'companyName', 'identifier']);
        }, ARRAY_FILTER_USE_KEY);

        return !empty($searchableFields);
    }

    public static function get(Rest $rest, int $customerId): Rest
    {
        $rest->get("customers/$customerId");

        return $rest;
    }

    public function search(Rest $rest): Rest
    {
        $rest->get('customers', $this->getData());

        return $rest;
    }

    public function create(Rest $rest): Rest
    {
        $rest->post('customers', $this->getData());

        return $rest;
    }

    public function update(Rest $rest, int $customerId): Rest
    {
        $rest->put("customers/$customerId", $this->getData());

        return $rest;
    }

    /**
     * Updates a customer by identifier or firstName and lastName if it exists;
     * otherwise, creates a new customer.
     */
    public static function updateOrCreate(Rest $rest, Customer $customer): Rest
    {
        // Search for customer
        $data = $customer->getData();
        $customer_search = new Customer();

        if (isset($data['identifier'])) {
            $customer_search->setIdentifier($data['identifier']);
        } elseif (isset($data['firstName']) && isset($data['lastName'])) {
            $customer_search->setFirstName($data['firstName']);
            $customer_search->setLastName($data['lastName']);
        }

        $existing = null;

        if ($customer_search->isSearchable()) {
            $search_results = $customer_search->search($rest)->getResult('results');

            if (!empty($search_results)) {
                $existing = $search_results[0];
            }
        }

        // Update existing customer
        if (isset($existing['customerId'])) {
            return $customer->update($rest, (int) $existing['customerId']);
        }

        return $customer->create($rest);
    }

    public static function delete(Rest $rest, int $customerId): Rest
    {
        $rest->delete("customers/$customerId");

        return $rest;
    }

    public function getData(): array
    {
        return array_filter(get_object_vars($this));
    }
}
