<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        $customers = $this->customerService->getAllCustomers();
        return new CustomerCollection($customers);
    }

    public function show($id)
    {
        $customer = $this->customerService->getCustomerById($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return new CustomerResource($customer);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $name = $request->input('name');

        $customer = $this->customerService->createCustomer(['name' => $name]);

        return (new CustomerResource($customer))
            ->additional(['message' => 'Customer created successfully']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $name = $request->input('name');

        $customer = $this->customerService->updateCustomer($id, ['name' => $name]);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return (new CustomerResource($customer))
            ->additional(['message' => 'Customer updated successfully']);
    }

    public function destroy($id)
    {
        $customer = $this->customerService->deleteCustomer($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
