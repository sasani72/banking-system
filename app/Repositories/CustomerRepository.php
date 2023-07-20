<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return Customer::create($data);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Customer::all();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return Customer::find($id);
    }

    /**
     * @param $id
     * @param array $data
     * @return |null
     */
    public function update($id, array $data)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return null;
        }

        $customer->update($data);
        return $customer;
    }

    /**
     * @param $id
     * @return bool|null
     */
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
