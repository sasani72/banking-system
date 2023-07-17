<?php

namespace App\Services;

use App\Repositories\CustomerRepository;

class CustomerService
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function createCustomer($data)
    {
        return $this->customerRepository->create($data);
    }

    public function getAllCustomers()
    {
        return $this->customerRepository->getAll();
    }

    public function getCustomerById($id)
    {
        return $this->customerRepository->getById($id);
    }

    public function updateCustomer($id, array $data)
    {
        return $this->customerRepository->update($id, $data);
    }

    public function deleteCustomer($id)
    {
        return $this->customerRepository->delete($id);
    }
}