<?php

namespace App\Services;

use App\Models\Operation;
use App\Models\Approval;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public function processApproval(array $data)
    {
        return DB::transaction(function () use ($data) {

            $operation = Operation::findOrFail($data['operation_id']);

            if ($operation->status !== 'pending') {
                abort(403, 'Operation already processed');
            }

            $approval = Approval::create([
                'operation_id' => $operation->id,
                'user_id' => auth()->id(),
                'status' => $data['status'],
                'comment' => $data['comment'] ?? null,
                'approved_at' => now(),
            ]);

            $operation->update(['status' => $data['status']]);

            $transaction = null;
            if ($data['status'] === 'approved') {
                $transaction = Transaction::create([
                    'operation_id' => $operation->id,
                    'customer_id' => $operation->customer_id,
                    'user_id' => auth()->id(),
                    'type' => $operation->type,
                    'amount' => $operation->amount,
                    'description' => $operation->description,
                ]);
            }

            return [$approval, $transaction];
        });
    }
}