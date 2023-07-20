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

    /**
     * @param $data
     * @return mixed
     */
    public function createCustomer($data)
    {
        return $this->customerRepository->create($data);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCustomers()
    {
        return $this->customerRepository->getAll();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCustomerById($id)
    {
        return $this->customerRepository->getById($id);
    }

    /**
     * @param $id
     * @param array $data
     * @return |null
     */
    public function updateCustomer($id, array $data)
    {
        return $this->customerRepository->update($id, $data);
    }

    /**
     * @param $id
     * @return bool|null
     */
    public function deleteCustomer($id)
    {
        return $this->customerRepository->delete($id);
    }
}
