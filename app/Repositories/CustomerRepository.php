<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function create($data)
    {
        return Customer::create($data);
    }

    public function getAll()
    {
        return Customer::all();
    }

    public function getById($id)
    {
        return Customer::find($id);
    }

    public function update($id, array $data)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return null;
        }

        $customer->update($data);
        return $customer;
    }

    public function delete($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return null;
        }

        $customer->delete();
        return true;
    }
}