<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    /**
     * عرض قائمة العمليات مع إمكانية البحث والفلترة.
     */
    public function index(Request $request)
    {
        $operations = Operation::with(['customer:id,name', 'user:id,name', 'category:id,name', 'subCategory:id,name'])
            ->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->filled('type'), function ($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->when($request->filled('customer_id'), function ($q) use ($request) {
                return $q->where('customer_id', $request->customer_id);
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                return $q->where('category_id', $request->category_id);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                return $q->where(function ($query) use ($search) {
                    $query->where('description', 'LIKE', "%{$search}%")
                          ->orWhereHas('customer', function ($q) use ($search) {
                              $q->where('name', 'LIKE', "%{$search}%");
                          });
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json($operations);
    }

    /**
     * إنشاء عملية جديدة.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type'        => 'required|in:receipt,payment',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $operation = Operation::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status'  => 'pending',
        ]);

        return response()->json(['message' => 'Created', 'operation' => $operation], 201);
    }

    /**
     * عرض عملية محددة.
     */
    public function show(Operation $operation)
    {
        return response()->json($operation->load(['customer', 'user', 'category']));
    }

    /**
     * تحديث عملية موجودة (فقط المعلقة).
     */
    public function update(Request $request, Operation $operation)
    {
        if ($operation->status !== 'pending') {
            abort(403, 'Only pending operations can be modified');
        }

        $validated = $request->validate([
            'description' => 'nullable|string',
            'amount'      => 'sometimes|numeric|min:0.01',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $operation->update($validated);

        return response()->json(['message' => 'Updated', 'operation' => $operation]);
    }

    /**
     * حذف عملية (فقط المعلقة).
     */
    public function destroy(Operation $operation)
    {
        if ($operation->status !== 'pending') {
            abort(403, 'Only pending operations can be modified');
        }

        $operation->delete();

        return response()->json(['message' => 'Deleted']);
    }
}