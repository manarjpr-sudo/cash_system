<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Customer;
use App\Models\Approval;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationViewController extends Controller
{
    public function index(Request $request)
    {
        $query = Operation::with(['customer', 'user']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Customer filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $operations = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $customers = Customer::orderBy('name')->get();

        return view('operations.index', compact(
            'operations',
            'customers'
        ));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();

        return view('operations.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $data['status'] = 'pending';
        $data['user_id'] = auth()->id();

        Operation::create($data);

        return redirect()
            ->route('operations.index')
            ->with('success', 'Operation created successfully.');
    }

    public function approve(Operation $operation)
    {
        return $this->processApproval($operation, 'approved');
    }

    public function reject(Operation $operation)
    {
        return $this->processApproval($operation, 'rejected');
    }

    private function processApproval(Operation $operation, string $status)
    {
        if ($operation->status !== 'pending') {
            return redirect()
                ->route('operations.index')
                ->with('error', 'This operation has already been processed.');
        }

        DB::transaction(function () use ($operation, $status) {

            Approval::create([
                'operation_id' => $operation->id,
                'user_id' => auth()->id(),
                'status' => $status,
                'approved_at' => now(),
            ]);

            $operation->update([
                'status' => $status,
            ]);

            if ($status === 'approved') {
                Transaction::create([
                    'operation_id' => $operation->id,
                    'customer_id' => $operation->customer_id,
                    'user_id' => auth()->id(),
                    'type' => $operation->type,
                    'amount' => $operation->amount,
                    'status' => 'completed',
                    'description' => $operation->description,
                ]);
            }
        });

        return redirect()
            ->route('operations.index')
            ->with(
                'success',
                $status === 'approved'
                    ? 'Operation approved and transaction created successfully.'
                    : 'Operation rejected successfully.'
            );
    }
}