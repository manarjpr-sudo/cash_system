<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return response()->json($customers);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'identity_number' => 'nullable|string|max:100|unique:customers',
            'room_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create($data);

        return response()->json([
            'message' => 'Customer created successfully',
            'customer' => $customer
        ], 201);
    }


    public function show(Customer $customer)
    {
        return response()->json(
            $customer->load('operations')
        );
    }


    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'identity_number' => 'nullable|string|max:100|unique:customers,identity_number,' . $customer->id,
            'room_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $customer->update($data);

        return response()->json([
            'message' => 'Customer updated successfully',
            'customer' => $customer
        ]);
    }


    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully'
        ]);
    }
}