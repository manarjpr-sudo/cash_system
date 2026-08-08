<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    public function index(Request $request)
    {
        $operations = Operation::with(['customer', 'user', 'approvals'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->customer_id, function ($query, $customerId) {
                $query->where('customer_id', $customerId);
            })
            ->latest()
            ->paginate(10);

        return response()->json($operations);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|in:receipt,payment,advance',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $operation = Operation::create([
            ...$data,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Operation created successfully',
            'operation' => $operation
        ], 201);
    }


    public function show(Operation $operation)
    {
        return response()->json(
            $operation->load([
                'customer',
                'user',
                'approvals'
            ])
        );
    }


    public function update(Request $request, Operation $operation)
    {
        if ($operation->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending operations can be updated'
            ], 403);
        }

        $data = $request->validate([
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $operation->update($data);

        return response()->json([
            'message' => 'Operation updated successfully',
            'operation' => $operation
        ]);
    }


    public function destroy(Operation $operation)
    {
        if ($operation->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending operations can be deleted'
            ], 403);
        }

        $operation->delete();

        return response()->json([
            'message' => 'Operation deleted successfully'
        ]);
    }
}